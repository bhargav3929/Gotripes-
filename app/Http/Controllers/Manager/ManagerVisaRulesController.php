<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DestinationVisaRule;
use App\Models\TravelPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Visa requirements shown in step 1 of the booking wizard, per destination
 * and passport. A rule for "Everyone else" (*) covers nationalities without
 * their own rule.
 */
class ManagerVisaRulesController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = TravelPackage::where('isActive', 1)->where('package_type', 'wizard')
            ->whereNotNull('country')->distinct()->orderBy('country')->pluck('country');
        $query = DestinationVisaRule::orderBy('destination')->orderByRaw("CASE WHEN nationality = '*' THEN 1 ELSE 0 END")->orderBy('nationality');
        if ($request->filled('destination')) {
            $query->where('destination', $request->query('destination'));
        }
        return view('manager.visa-rules.index', [
            'rules'        => $query->paginate(50)->withQueryString(),
            'destinations' => $destinations,
            'countries'    => array_keys(config('countries')),
            'requirements' => DestinationVisaRule::REQUIREMENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DestinationVisaRule::updateOrCreate(
            ['destination' => $data['destination'], 'nationality' => $data['nationality']],
            $data
        );
        return back()->with('success', 'Visa rule saved for ' . ($data['nationality'] === '*' ? 'everyone else' : $data['nationality']) . ' → ' . $data['destination'] . '.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $rule = DestinationVisaRule::findOrFail($id);
        $rule->update($this->validated($request, $rule->id));
        return back()->with('success', 'Visa rule updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        DestinationVisaRule::findOrFail($id)->delete();
        return back()->with('success', 'Visa rule removed.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $countries = array_keys(config('countries'));
        return $request->validate([
            'destination'     => ['required', Rule::in($countries)],
            'nationality'     => ['required', Rule::in(array_merge(['*'], $countries))],
            'requirement'     => ['required', Rule::in(array_keys(DestinationVisaRule::REQUIREMENTS))],
            'max_stay_days'   => 'nullable|integer|min:1|max:3650',
            'processing_days' => 'nullable|integer|min:0|max:365',
            'fee'             => 'nullable|numeric|min:0|max:100000',
            'documents'       => 'nullable|string|max:1000',
            'notes'           => 'nullable|string|max:1000',
        ]);
    }
}
