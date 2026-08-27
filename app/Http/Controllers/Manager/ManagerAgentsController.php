<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TravelPackage;
use App\Models\UAEActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * "Add Agent" — tenant managers create agent accounts and pick which
 * services (tours / activities / esim) each agent may use in the /agent
 * portal. Credentials are shown once after creation so the manager can
 * share them with the agent.
 *
 * The users table has no BelongsToCompany trait, so every query here is
 * explicitly scoped to the manager's company.
 */
class ManagerAgentsController extends Controller
{
    /**
     * The tenant this manager administers. current_company() is bound by
     * IdentifyTenant on tenant domains; fall back to the manager's own
     * company_id so the page also works for super admins on the main domain.
     */
    private function companyId(): int
    {
        return (int) (current_company_id() ?: auth()->user()->company_id);
    }

    /**
     * Services this tenant can grant: the intersection of the canonical
     * agent services with the company's enabled features. 'evisa' and
     * 'uae_visa' have no feature of their own — both piggyback on 'visas'
     * (see User::hasService()).
     */
    public static function grantableServicesFor(?Company $company): array
    {
        return array_filter(
            User::AGENT_SERVICES,
            fn ($key) => $company === null || $company->hasFeature(User::featureKeyForService($key)),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function grantableServices(): array
    {
        return self::grantableServicesFor(current_company() ?: auth()->user()->company);
    }

    /**
     * Supports the meeting requirement that managers be able to filter
     * agents by location, service, and trade license expiry — each stored
     * in its own column rather than a blob, so these are plain where()s.
     */
    public function index(Request $request)
    {
        $location = $request->get('location');
        $service = $request->get('service');
        $licenseStatus = $request->get('license_status');
        $today = now()->startOfDay();

        $agents = User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->when($location, fn ($q) => $q->where(function ($q2) use ($location) {
                $q2->where('emirate', $location)->orWhere('country', $location);
            }))
            ->when($service, fn ($q) => $q->whereJsonContains('agent_services', $service))
            ->when($licenseStatus === 'expired', fn ($q) => $q->whereNotNull('trade_license_expiry_date')->where('trade_license_expiry_date', '<', $today))
            ->when($licenseStatus === 'expiring_soon', fn ($q) => $q->whereNotNull('trade_license_expiry_date')->whereBetween('trade_license_expiry_date', [$today, $today->copy()->addDays(30)]))
            ->when($licenseStatus === 'renewal_pending', fn ($q) => $q->where('pending_license_review', true))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Listing counts per agent, shown on the index table.
        $agentIds = $agents->pluck('id');
        $packageCounts = TravelPackage::withoutCompanyScope()
            ->whereIn('agent_id', $agentIds)
            ->where('isActive', 1)
            ->selectRaw('agent_id, COUNT(*) as c')
            ->groupBy('agent_id')
            ->pluck('c', 'agent_id');
        $activityCounts = UAEActivity::withoutCompanyScope()
            ->whereIn('agent_id', $agentIds)
            ->where('isActive', 1)
            ->selectRaw('agent_id, COUNT(*) as c')
            ->groupBy('agent_id')
            ->pluck('c', 'agent_id');

        $locations = User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->whereNotNull('emirate')
            ->distinct()
            ->pluck('emirate')
            ->merge(
                User::where('company_id', $this->companyId())
                    ->where('role', 'company_agent')
                    ->whereNotNull('country')
                    ->whereNull('emirate')
                    ->distinct()
                    ->pluck('country')
            )
            ->unique()
            ->sort()
            ->values();

        return view('manager.agents.index', compact('agents', 'packageCounts', 'activityCounts', 'locations', 'location', 'service', 'licenseStatus'));
    }

    public function create()
    {
        $services = $this->grantableServices();
        return view('manager.agents.create', compact('services'));
    }

    public function store(Request $request)
    {
        $services = $this->grantableServices();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email',
            'phone'      => 'nullable|string|max:30',
            'password'   => 'required|string|min:8|max:72',
            'services'   => 'required|array|min:1',
            'services.*' => ['string', Rule::in(array_keys($services))],
        ], [
            'services.required' => 'Select at least one service for this agent.',
        ]);

        User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'] ?? null,
            'password'       => Hash::make($validated['password']),
            'role'           => 'company_agent',
            'company_id'     => $this->companyId(),
            'agent_services' => array_values($validated['services']),
            'is_active'      => true,
        ]);

        // Shown exactly once so the manager can copy and share the login.
        return redirect()->route('manager.agents.index')
            ->with('success', 'Agent account created.')
            ->with('agent_credentials', [
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'],
                'url'      => route('agent.login'),
            ]);
    }

    public function edit($id)
    {
        $agent = $this->findAgent($id);
        $services = $this->grantableServices();
        return view('manager.agents.edit', compact('agent', 'services'));
    }

    public function update(Request $request, $id)
    {
        $agent = $this->findAgent($id);
        $services = $this->grantableServices();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($agent->id)],
            'phone'      => 'nullable|string|max:30',
            'password'   => 'nullable|string|min:8|max:72',
            'services'   => 'required|array|min:1',
            'services.*' => ['string', Rule::in(array_keys($services))],
            'is_active'  => 'nullable|boolean',
        ], [
            'services.required' => 'Select at least one service for this agent.',
        ]);

        $agent->fill([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'phone'          => $validated['phone'] ?? null,
            'agent_services' => array_values($validated['services']),
            'is_active'      => $request->boolean('is_active'),
        ]);

        if (!empty($validated['password'])) {
            $agent->password = Hash::make($validated['password']);
        }

        $agent->save();

        $message = 'Agent updated.';
        if (!empty($validated['password'])) {
            $message .= ' New password set — share it with the agent.';
        }

        return redirect()->route('manager.agents.index')->with('success', $message);
    }

    /**
     * Deactivate (not delete) — the agent's listings stay live and
     * attributed; the account simply can no longer log in.
     */
    public function destroy($id)
    {
        $agent = $this->findAgent($id);
        $agent->update(['is_active' => false]);

        return redirect()->route('manager.agents.index')
            ->with('success', "Agent “{$agent->name}” deactivated. Their listings remain live.");
    }

    /**
     * Confirm a renewed trade license submitted through the self-service
     * renewal flow — restores access. Mirrors
     * ManagerB2bPartnersController::confirmRenewal().
     */
    public function confirmRenewal($id)
    {
        $agent = $this->findAgent($id);

        if (!$agent->pending_license_review) {
            return redirect()->route('manager.agents.index')
                ->with('error', 'This agent has no renewal awaiting confirmation.');
        }

        $agent->confirmLicenseRenewal();

        return redirect()->route('manager.agents.index')
            ->with('success', "\"{$agent->name}\"'s renewed license confirmed — their account is active again.");
    }

    private function findAgent($id): User
    {
        return User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->findOrFail($id);
    }
}
