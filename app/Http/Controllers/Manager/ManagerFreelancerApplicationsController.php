<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\FreelancerApplicationDecisionMail;
use App\Models\ContractDocument;
use App\Models\ReferralAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Manager-facing review queue for self-registered freelancer applications
 * (`/freelancer/register`). Tenant-scoped automatically via BelongsToCompany
 * on ReferralAgent. Mirrors ManagerAgentApplicationsController.
 */
class ManagerFreelancerApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending_review');

        $applications = ReferralAgent::where('is_freelancer', true)
            ->when($status !== 'all', fn ($q) => $q->where('application_status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('manager.freelancer-applications.index', compact('applications', 'status'));
    }

    public function show(ReferralAgent $agent)
    {
        abort_unless($agent->is_freelancer, 404);

        return view('manager.freelancer-applications.show', ['application' => $agent]);
    }

    /**
     * Rules for the eSIM margin a manager grants. Percentage is of the platform
     * price; fixed is AED per eSIM. Capped so a typo can't price a plan absurdly.
     */
    private function marginRules(): array
    {
        return [
            'esim_margin_type'  => 'required|in:fixed,percentage',
            'esim_margin_value' => ['required', 'numeric', 'min:0', request('esim_margin_type') === 'percentage' ? 'max:100' : 'max:1000'],
        ];
    }

    public function approve(Request $request, ReferralAgent $agent)
    {
        abort_unless($agent->is_freelancer, 404);

        if (!$agent->isPendingApplication()) {
            return redirect()->route('manager.freelancer-applications.show', $agent->id)
                ->with('error', 'This application has already been reviewed.');
        }

        // Contract first, access after — same rule as the agent flow.
        if (ContractDocument::current() && !$agent->hasSignedContract()) {
            return redirect()->route('manager.freelancer-applications.show', $agent->id)
                ->with('error', 'This applicant has not signed the agreement, so they cannot be approved. Ask them to register again now that the contract is published.');
        }

        $margin = $request->validate($this->marginRules());

        $agent->update($margin);
        $agent->approveApplication(auth()->user());

        $this->sendDecisionMail($agent, 'approved');

        return redirect()->route('manager.freelancer-applications.index')
            ->with('success', "\"{$agent->name}\" approved. Their storefront is now active.");
    }

    /** Change an approved freelancer's eSIM margin. Applies to future sales only. */
    public function updateMargin(Request $request, ReferralAgent $agent)
    {
        abort_unless($agent->is_freelancer, 404);

        $agent->update($request->validate($this->marginRules()));

        return redirect()->route('manager.freelancer-applications.show', $agent->id)
            ->with('success', "eSIM margin for \"{$agent->name}\" is now {$agent->esimMarginLabel()}. It applies to new sales only.");
    }

    public function reject(Request $request, ReferralAgent $agent)
    {
        abort_unless($agent->is_freelancer, 404);

        if (!$agent->isPendingApplication()) {
            return redirect()->route('manager.freelancer-applications.show', $agent->id)
                ->with('error', 'This application has already been reviewed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $agent->rejectApplication(auth()->user(), $validated['reason']);

        $this->sendDecisionMail($agent, 'rejected');

        return redirect()->route('manager.freelancer-applications.index')
            ->with('success', "\"{$agent->name}\" rejected.");
    }

    private function sendDecisionMail(ReferralAgent $agent, string $decision): void
    {
        try {
            Mail::to($agent->email)->send(new FreelancerApplicationDecisionMail($agent, $decision));
        } catch (\Throwable $e) {
            Log::error('Freelancer application decision email failed', [
                'agent_id' => $agent->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
