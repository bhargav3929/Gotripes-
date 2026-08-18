<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manager\ManagerAgentsController;
use App\Mail\AgentApplicationReceivedMail;
use App\Models\AgentApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * Public self-service agent registration: an applicant picks which services
 * (Tours / Activities / eSIM / Global e-Visa / UAE Visa) interest them,
 * uploads a trade license, and submits. This does NOT create a working
 * `company_agent` login — the application sits `pending` until a manager
 * reviews the license and approves it (see ManagerAgentApplicationsController).
 * Modeled on B2bPartnerSignupController, minus the contract/e-signature
 * flow, which agent registration wasn't asked to have.
 */
class AgentSignupController extends Controller
{
    public function showRegister()
    {
        if (Auth::check() && Auth::user()->role === 'company_agent') {
            return redirect()->route('agent.dashboard');
        }

        $services = ManagerAgentsController::grantableServicesFor(current_company());

        return view('agent.register', compact('services'));
    }

    public function register(Request $request)
    {
        $services = ManagerAgentsController::grantableServicesFor(current_company());

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:agent_applications,email|unique:users,email',
            'phone'    => 'required|string|max:30',
            'country'  => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'services'   => 'required|array|min:1',
            'services.*' => ['string', Rule::in(array_keys($services))],
            'trade_license_number'       => 'required|string|max:100',
            'trade_license_expiry_date'  => 'required|date|after:today',
            'trade_license_document'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'services.required' => 'Select at least one service you\'re interested in.',
        ]);

        $licensePath = $request->file('trade_license_document')->store('agents/trade_licenses', 'public');

        $application = AgentApplication::create([
            'company_id' => current_company_id(),
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'phone'      => $validated['phone'],
            'country'    => $validated['country'] ?? null,
            'services'   => array_values($validated['services']),
            'trade_license_number'      => $validated['trade_license_number'],
            'trade_license_expiry_date' => $validated['trade_license_expiry_date'],
            'trade_license_document_path' => $licensePath,
            'status'     => 'pending',
        ]);

        try {
            Mail::to($application->email)->send(new AgentApplicationReceivedMail($application));
        } catch (\Throwable $e) {
            Log::error('Agent application received email failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('agent.register.submitted');
    }

    public function submitted()
    {
        return view('agent.register-submitted');
    }
}
