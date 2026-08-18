<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\AgentApplicationDecisionMail;
use App\Models\AgentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Manager-facing review queue for self-registered agent applications.
 * Tenant-scoped automatically via BelongsToCompany's global scope on
 * AgentApplication, same protection ManagerB2bPartnersController gets for
 * B2B partners.
 */
class ManagerAgentApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $applications = AgentApplication::when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('manager.agent-applications.index', compact('applications', 'status'));
    }

    public function show(AgentApplication $application)
    {
        return view('manager.agent-applications.show', compact('application'));
    }

    public function approve(AgentApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->route('manager.agent-applications.show', $application->id)
                ->with('error', 'This application has already been reviewed.');
        }

        $application->approve(auth()->user());

        $this->sendDecisionMail($application, 'approved');

        return redirect()->route('manager.agent-applications.index')
            ->with('success', "\"{$application->name}\" approved. Their agent account is now active.");
    }

    public function reject(Request $request, AgentApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->route('manager.agent-applications.show', $application->id)
                ->with('error', 'This application has already been reviewed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $application->reject(auth()->user(), $validated['reason']);

        $this->sendDecisionMail($application, 'rejected');

        return redirect()->route('manager.agent-applications.index')
            ->with('success', "\"{$application->name}\" rejected.");
    }

    private function sendDecisionMail(AgentApplication $application, string $decision): void
    {
        try {
            Mail::to($application->email)->send(new AgentApplicationDecisionMail($application, $decision));
        } catch (\Throwable $e) {
            Log::error('Agent application decision email failed', [
                'application_id' => $application->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
