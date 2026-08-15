<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\B2bPartnerApplicationDecisionMail;
use App\Models\B2bPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Manager-facing review queue for B2B partner applications. Tenant-scoped
 * automatically via BelongsToCompany's global scope on B2bPartner — a
 * manager can never see or act on another tenant's applications, the same
 * protection ManagerAgentsController gets for free.
 */
class ManagerB2bPartnersController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $partners = B2bPartner::when($status === 'renewal', fn ($q) => $q->where('pending_license_review', true))
            ->when(!in_array($status, ['all', 'renewal'], true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('manager.b2b-partners.index', compact('partners', 'status'));
    }

    public function show(B2bPartner $partner)
    {
        return view('manager.b2b-partners.show', compact('partner'));
    }

    public function approve(B2bPartner $partner)
    {
        $partner->approve(auth()->user());

        $this->sendDecisionMail($partner, 'approved');

        return redirect()->route('manager.b2b-partners.index')
            ->with('success', "\"{$partner->company_name}\" approved. They can now log in.");
    }

    public function reject(Request $request, B2bPartner $partner)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $partner->reject(auth()->user(), $validated['reason']);

        $this->sendDecisionMail($partner, 'rejected');

        return redirect()->route('manager.b2b-partners.index')
            ->with('success', "\"{$partner->company_name}\" rejected.");
    }

    public function confirmRenewal(B2bPartner $partner)
    {
        if (!$partner->pending_license_review) {
            return redirect()->route('manager.b2b-partners.show', $partner->id)
                ->with('error', 'This partner has no renewal awaiting confirmation.');
        }

        $partner->confirmRenewal(auth()->user());

        return redirect()->route('manager.b2b-partners.index')
            ->with('success', "\"{$partner->company_name}\"'s renewed license confirmed — their account is active again.");
    }

    public function updateCommission(Request $request, B2bPartner $partner)
    {
        $validated = $request->validate([
            'commission_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $partner->update(['commission_percent' => $validated['commission_percent'] ?? null]);

        return redirect()->route('manager.b2b-partners.show', $partner->id)
            ->with('success', 'Commission updated.');
    }

    private function sendDecisionMail(B2bPartner $partner, string $decision): void
    {
        try {
            Mail::to($partner->email)->send(new B2bPartnerApplicationDecisionMail($partner, $decision));
        } catch (\Throwable $e) {
            Log::error('B2B partner decision email failed', [
                'partner_id' => $partner->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
