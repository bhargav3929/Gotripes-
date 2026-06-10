<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
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
     * agent services with the company's enabled features.
     */
    private function grantableServices(): array
    {
        $company = current_company() ?: auth()->user()->company;

        return array_filter(
            User::AGENT_SERVICES,
            fn ($key) => $company === null || $company->hasFeature($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function index()
    {
        $agents = User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->orderByDesc('id')
            ->paginate(15);

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

        return view('manager.agents.index', compact('agents', 'packageCounts', 'activityCounts'));
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

    private function findAgent($id): User
    {
        return User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->findOrFail($id);
    }
}
