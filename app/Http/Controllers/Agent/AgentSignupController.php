<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Manager\ManagerAgentsController;
use App\Mail\AgentApplicationReceivedMail;
use App\Models\AgentApplication;
use App\Models\Emirates;
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
        $emirates = Emirates::getActiveEmirates();
        $countries = collect(config('countries', []))
            ->keys()
            ->reject(fn ($c) => $c === AgentApplication::UAE_COUNTRY_NAME)
            ->values();

        return view('agent.register', compact('services', 'emirates', 'countries'));
    }

    public function register(Request $request)
    {
        $services = ManagerAgentsController::grantableServicesFor(current_company());
        $emirateNames = Emirates::getActiveEmirates()->pluck('emiratesName')->all();
        $isUae = $request->boolean('registering_from_uae');

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'company_name'  => 'required|string|max:255',
            'email'         => 'required|email|unique:agent_applications,email|unique:users,email',
            'phone'         => 'required|string|max:30',
            'address'       => 'required|string|max:500',
            'registering_from_uae' => 'required|boolean',
            'emirate'       => ['required_if:registering_from_uae,1', 'nullable', 'string', Rule::in($emirateNames)],
            'country'       => ['required_if:registering_from_uae,0', 'nullable', 'string', 'max:100'],
            'password'      => 'required|string|min:8|confirmed',
            'services'      => 'required|array|min:1',
            'services.*'    => ['string', Rule::in(array_keys($services))],
            'trade_license_number'       => 'required|string|max:100',
            'trade_license_expiry_date'  => 'required|date|after:today',
            'trade_license_document'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'services.required' => 'Select at least one service you\'re interested in.',
            'emirate.required_if' => 'Select which Emirate you\'re registering from.',
            'country.required_if' => 'Select which country you\'re registering from.',
        ]);

        $licensePath = $request->file('trade_license_document')->store('agents/trade_licenses', 'public');

        $application = AgentApplication::create([
            'company_id'   => current_company_id(),
            'name'         => $validated['name'],
            'company_name' => $validated['company_name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'phone'        => $validated['phone'],
            'address'      => $validated['address'],
            'country'      => $isUae ? AgentApplication::UAE_COUNTRY_NAME : $validated['country'],
            'emirate'      => $isUae ? $validated['emirate'] : null,
            'services'     => array_values($validated['services']),
            'trade_license_number'      => $validated['trade_license_number'],
            'trade_license_expiry_date' => $validated['trade_license_expiry_date'],
            'trade_license_document_path' => $licensePath,
            'status'       => 'pending',
        ]);

        try {
            Mail::to($application->email)->send(new AgentApplicationReceivedMail($application));
        } catch (\Throwable $e) {
            Log::error('Agent application received email failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }

        // The public homepage's "Create Partner Account" modal submits this
        // same endpoint via AJAX (Accept: application/json) instead of the
        // full-page form post this controller was originally built for —
        // give it a JSON body instead of a redirect it can't follow.
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('agent.register.submitted');
    }

    public function submitted()
    {
        return view('agent.register-submitted');
    }
}
