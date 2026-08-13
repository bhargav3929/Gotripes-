<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EvisaSetting;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * "Global e-Visa" — kept separate from UAE Visa package pricing. Holds the
 * platform-wide Fluxir e-Visa markup (its edit form moved here from the old
 * visa-pricing page) and lets a manager set a per-agent e-Visa commission,
 * reusing the existing Manager -> Agents (company_agent) accounts rather
 * than building a parallel agent/B2B system.
 */
class ManagerEvisaSettingsController extends Controller
{
    private function companyId(): int
    {
        return (int) (current_company_id() ?: auth()->user()->company_id);
    }

    public function index()
    {
        $evisaMarkup = EvisaSetting::markupPercent();

        $agents = User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->orderBy('name')
            ->get();

        return view('manager.evisa-settings.index', compact('evisaMarkup', 'agents'));
    }

    /**
     * Bulk-save every agent's e-Visa access + commission in one submit,
     * mirroring ManagerVisaPricingController::bulkUpdatePriceRow()'s pattern.
     * Tenant-scoped: only this company's company_agent users can be touched.
     */
    public function updateAgentCommissions(Request $request)
    {
        $validated = $request->validate([
            'agents'                       => 'nullable|array',
            'agents.*.enabled'             => 'nullable|boolean',
            'agents.*.commission_percent'  => 'nullable|numeric|min:0|max:100',
        ]);

        $agentRows = $validated['agents'] ?? [];

        $agents = User::where('company_id', $this->companyId())
            ->where('role', 'company_agent')
            ->whereIn('id', array_keys($agentRows))
            ->get();

        foreach ($agents as $agent) {
            $row = $agentRows[$agent->id];

            $services = is_array($agent->agent_services) ? $agent->agent_services : [];
            $services = array_values(array_diff($services, ['evisa']));
            if (!empty($row['enabled'])) {
                $services[] = 'evisa';
            }

            $agent->agent_services = $services;
            $agent->evisa_commission_percent = $row['commission_percent'] ?? null;
            $agent->save();
        }

        return redirect()->route('manager.evisa-settings.index')
            ->with('success', 'Agent e-Visa commissions updated.');
    }
}
