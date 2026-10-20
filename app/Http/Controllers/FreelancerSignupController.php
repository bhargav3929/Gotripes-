<?php

namespace App\Http\Controllers;

use App\Mail\FreelancerApplicationReceivedMail;
use App\Models\ContractDocument;
use App\Models\ReferralAgent;
use App\Models\ReferralBankAccount;
use App\Models\ReferralSetting;
use App\Models\User;
use App\Notifications\NewApplicationSubmitted;
use App\Services\AgentContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Freelancer signup — the circular pop-up on freelancer.landing (and, as a
 * fallback for anyone who lands on /freelancer/register directly, that same
 * page with the pop-up pre-opened). Stores an agent with is_freelancer=true;
 * reuses partner referral plumbing.
 *
 * Since 27 Sep 2026 this is gated the same way /agent/register already is
 * (docs/client-feedback-2026-09-22.md): the applicant signs the current
 * contract, the account sits `status = 'inactive'` / `application_status =
 * 'pending_review'` until a manager approves it (Manager -> Freelancer
 * Applications), and no one is auto-logged in at registration anymore.
 */
class FreelancerSignupController extends Controller
{
    public function landing()
    {
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }

        $contract = ContractDocument::current();
        $countries = collect(config('countries', []))->keys()->values();

        return view('freelancer.landing', compact('contract', 'countries'));
    }

    /**
     * /freelancer/register no longer has its own page — it's the same pop-up
     * that lives on the landing page, just pre-opened, so old links and the
     * sitewide header CTA still land somewhere that works.
     */
    public function showRegister()
    {
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }

        return redirect()->route('freelancer.landing', ['open' => 'register']);
    }

    public function register(Request $request)
    {
        $settings = ReferralSetting::getSettings();
        if (!$settings->signup_enabled) {
            $message = 'Registrations are currently closed.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('freelancer.landing')->with('error', $message);
        }

        $contract = ContractDocument::current();

        $contractRules = $contract ? [
            'signature_full_name' => 'required|string|max:255',
            'signature_agreed'    => 'accepted',
        ] : [];

        $validated = $request->validate($contractRules + [
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:referral_agents,email',
            'phone'               => 'required|string|max:20',
            'country'             => 'required|string|max:100',
            'password'            => 'required|min:8|confirmed',
            'bank_name'           => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number'      => 'required|string|max:64',
            'iban'                => 'nullable|string|max:64',
            'swift_code'          => 'nullable|string|max:32',
        ], [
            'signature_full_name.required' => 'Type your full name to sign the agreement.',
            'signature_agreed.accepted' => 'You need to accept the agreement before we can review your application.',
        ]);

        $agent = DB::transaction(function () use ($validated, $settings, $contract, $request) {
            $agent = ReferralAgent::create([
                'name'             => $validated['name'],
                'email'            => $validated['email'],
                'phone'            => $validated['phone'],
                'country'          => $validated['country'],
                'is_freelancer'    => true,
                'password'         => Hash::make($validated['password']),
                'commission_type'  => $settings->commission_type,
                'commission_value' => $settings->commission_value,
                // 'inactive' (not 'active'): ReferralAgentDashboardController's
                // login already refuses any non-'active' account, so this alone
                // gates access until a manager approves the application.
                'status'           => 'inactive',
                'application_status'   => 'pending_review',
                'referral_code'    => ReferralAgent::generateCodeFromName($validated['name']),
                'contract_document_id' => $contract?->id,
                'signature_full_name'  => $contract ? $validated['signature_full_name'] : null,
                'signature_agreed'     => (bool) $contract,
                'signature_ip'         => $contract ? $request->ip() : null,
                'signed_at'            => $contract ? now() : null,
            ]);

            ReferralBankAccount::create([
                'referral_agent_id'   => $agent->id,
                'bank_name'           => $validated['bank_name'],
                'account_holder_name' => $validated['account_holder_name'],
                'account_number'      => $validated['account_number'],
                'iban'                => $validated['iban'] ?? null,
                'swift_code'          => $validated['swift_code'] ?? null,
                'country'             => $validated['country'],
                'is_primary'          => true,
            ]);

            return $agent;
        });

        if ($contract) {
            $agent->update([
                'signed_pdf_path' => app(AgentContractService::class)->sign($agent, $contract),
            ]);
        }

        try {
            Mail::to($agent->email)->send(new FreelancerApplicationReceivedMail($agent));
        } catch (\Throwable $e) {
            Log::error('Freelancer application received email failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $admins = User::companyAdmins($agent->company_id);
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewApplicationSubmitted(
                    type: 'freelancer',
                    applicantName: $agent->name,
                    applicantEmail: $agent->email,
                    reviewUrl: route('manager.freelancer-applications.show', $agent->id),
                ));
            }
        } catch (\Throwable $e) {
            Log::error('Freelancer application admin notification failed', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage(),
            ]);
        }

        // The landing page's pop-up submits this via AJAX (Accept: application/json).
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('freelancer.register.submitted');
    }

    public function submitted()
    {
        return view('freelancer.register-submitted');
    }
}
