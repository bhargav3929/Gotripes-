<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EvisaSetting;
use App\Models\UAEVisaMaster;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;
use Illuminate\Http\Request;

class ManagerVisaPricingController extends Controller
{
    /**
     * Emirates that carry a refundable security deposit.
     *
     * Sharjah is the only one today, but the client asked to be able to add
     * others without a code change, so the deposit fields are simply revealed
     * for any emirate named here — and a manager can still type a deposit on
     * any package if an emirate starts requiring one mid-season.
     */
    public const DEPOSIT_EMIRATES = ['sharjah'];

    public function index()
    {
        $emirates = Emirates::where('isActive', 1)->orderBy('emiratesName')->get();
        $packages = UAEVisaPackage::with(['emirate', 'prices'])->orderBy('name')->get();
        $prices   = UAEVisaPrice::with('package.emirate')->get();

        $company = current_company();
        $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
        $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;

        // Global markup applied to every Fluxir e-Visa (the /e-visa storefront).
        $evisaMarkup = EvisaSetting::markupPercent();

        // Emirate IDs whose packages should reveal the deposit fields.
        $depositEmirateIds = $emirates
            ->filter(fn($e) => in_array(strtolower(trim($e->emiratesName)), self::DEPOSIT_EMIRATES, true))
            ->pluck('emiratesID')
            ->values();

        return view('manager.visa-pricing.index', compact(
            'emirates',
            'packages',
            'prices',
            'hotelFee',
            'ticketFee',
            'evisaMarkup',
            'depositEmirateIds'
        ));
    }

    /** Update the global e-Visa (Fluxir) markup percentage. */
    public function updateEvisaMarkup(Request $request)
    {
        $validated = $request->validate([
            'markup_percent' => 'required|numeric|min:0|max:1000',
        ]);

        EvisaSetting::current()->update(['markup_percent' => $validated['markup_percent']]);

        return back()->with('success', 'e-Visa markup updated to ' . rtrim(rtrim(number_format($validated['markup_percent'], 2), '0'), '.') . '%.');
    }

    /**
     * The two add-on fees that are genuinely global (they apply to every
     * package regardless of emirate). Everything else that used to live on the
     * old "Add-On & Settings" tab is now configured per package.
     */
    public function updateServiceFees(Request $request)
    {
        $validated = $request->validate([
            'visa_hotel_booking_fee'  => 'required|numeric|min:0',
            'visa_ticket_booking_fee' => 'required|numeric|min:0',
        ]);

        $company = current_company();

        $settings = $company->settings ?? [];
        $settings['visa_hotel_booking_fee']  = (float) $validated['visa_hotel_booking_fee'];
        $settings['visa_ticket_booking_fee'] = (float) $validated['visa_ticket_booking_fee'];
        $company->settings = $settings;
        $company->save();

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Add-on service fees updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'UAEVisaDuration' => 'required|string|max:100',
            'UAEVPrice'       => 'required|numeric|min:0',
        ]);

        UAEVisaMaster::create([
            'UAEVisaDuration' => $validated['UAEVisaDuration'],
            'UAEVPrice'       => $validated['UAEVPrice'],
            'isActive'        => 1,
            'createdBy'       => auth()->user()?->name ?? 'manager',
            'createdDate'     => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price added.');
    }

    public function update(Request $request, $id)
    {
        $visa = UAEVisaMaster::where('vID', $id)->where('isActive', 1)->firstOrFail();

        $validated = $request->validate([
            'UAEVisaDuration' => 'required|string|max:100',
            'UAEVPrice'       => 'required|numeric|min:0',
        ]);

        $visa->update([
            'UAEVisaDuration' => $validated['UAEVisaDuration'],
            'UAEVPrice'       => $validated['UAEVPrice'],
            'modifiedBy'      => auth()->user()?->name ?? 'manager',
            'modifiedDate'    => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price updated.');
    }

    public function destroy($id)
    {
        $visa = UAEVisaMaster::where('vID', $id)->where('isActive', 1)->firstOrFail();

        $visa->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price removed.');
    }

    // --- Emirates CRUD ---
    public function storeEmirate(Request $request)
    {
        $validated = $request->validate([
            'emiratesName'        => 'required|string|max:100|unique:tbl_emirates,emiratesName',
            'emiratesDescription' => 'nullable|string|max:1000',
            'emiratesImage'       => 'nullable|string|max:255',
        ]);

        Emirates::create([
            'emiratesName'        => $validated['emiratesName'],
            'emiratesDescription' => $validated['emiratesDescription'] ?? '',
            'emiratesImage'       => $validated['emiratesImage'] ?? '',
            'country'             => 'United Arab Emirates',
            'isActive'            => 1,
            'createdBy'           => auth()->user()?->name ?? 'manager',
            'createdDate'         => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate added successfully.');
    }

    public function updateEmirate(Request $request, $id)
    {
        $emirate = Emirates::findOrFail($id);

        $validated = $request->validate([
            'emiratesName'        => 'required|string|max:100|unique:tbl_emirates,emiratesName,' . $id . ',emiratesID',
            'emiratesDescription' => 'nullable|string|max:1000',
            'emiratesImage'       => 'nullable|string|max:255',
            'isActive'            => 'required|boolean',
        ]);

        $emirate->update([
            'emiratesName'        => $validated['emiratesName'],
            'emiratesDescription' => $validated['emiratesDescription'] ?? '',
            'emiratesImage'       => $validated['emiratesImage'] ?? '',
            'isActive'            => $validated['isActive'],
            'modifiedBy'          => auth()->user()?->name ?? 'manager',
            'modifiedDate'        => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate updated successfully.');
    }

    public function destroyEmirate($id)
    {
        $emirate = Emirates::findOrFail($id);
        $emirate->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate deactivated.');
    }

    // --- Packages CRUD ---

    /**
     * Validation shared by create and edit. `emails` allows a comma-separated
     * list so a package can notify two suppliers at once.
     */
    private function packageRules(bool $forUpdate = false): array
    {
        $rules = [
            'emirates_id'       => 'required|exists:tbl_emirates,emiratesID',
            'name'              => 'required|string|max:100',
            'package_type'      => 'required|in:Standard,Urgent',
            'description'       => 'nullable|string|max:1000',
            'security_deposit'  => 'nullable|numeric|min:0',
            'deposit_admin_fee' => 'nullable|numeric|min:0|lte:security_deposit',
            'supplier_email'    => 'nullable|string|max:255',
            'company_email'     => 'nullable|string|max:255',
        ];

        if ($forUpdate) {
            $rules['isActive'] = 'required|boolean';
        }

        return $rules;
    }

    private function packageMessages(): array
    {
        return [
            'deposit_admin_fee.lte' => 'The processing fee cannot be greater than the security deposit.',
        ];
    }

    /**
     * Normalise the optional package fields: '' becomes null so the model falls
     * back to the company-wide setting, while an explicit 0 is preserved as
     * "this package charges no deposit".
     */
    private function packageAttributes(array $validated): array
    {
        $nullIfBlank = fn($v) => ($v === null || $v === '') ? null : $v;

        return [
            'emirates_id'       => $validated['emirates_id'],
            'name'              => $validated['name'],
            'package_type'      => $validated['package_type'],
            'description'       => $validated['description'] ?? null,
            'security_deposit'  => $nullIfBlank($validated['security_deposit'] ?? null),
            'deposit_admin_fee' => $nullIfBlank($validated['deposit_admin_fee'] ?? null),
            'supplier_email'    => $nullIfBlank(trim((string) ($validated['supplier_email'] ?? ''))),
            'company_email'     => $nullIfBlank(trim((string) ($validated['company_email'] ?? ''))),
        ];
    }

    /**
     * Create a package and, in the same submit, its first price row.
     *
     * The client works one visa at a time — "Sharjah, urgent, adult, 30 days,
     * 450 AED, this supplier" — so splitting that across two forms on two tabs
     * meant every new visa took two trips. The matrix tab then handles ongoing
     * price edits.
     */
    public function storePackage(Request $request)
    {
        $validated = $request->validate(
            $this->packageRules() + [
                'entry_type'     => 'required|string|max:100',
                'duration'       => 'required|string|max:100',
                'traveller_type' => 'required|string|max:100',
                'price'          => 'required|numeric|min:0',
            ],
            $this->packageMessages()
        );

        $package = UAEVisaPackage::create(
            $this->packageAttributes($validated) + ['isActive' => 1]
        );

        UAEVisaPrice::create([
            'visa_package_id' => $package->id,
            'entry_type'      => $validated['entry_type'],
            'duration'        => $validated['duration'],
            'traveller_type'  => $validated['traveller_type'],
            'price'           => $validated['price'],
            'isActive'        => 1,
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', "\"{$package->name}\" created with its first price row. Add more traveller types or durations below.");
    }

    public function updatePackage(Request $request, $id)
    {
        $package = UAEVisaPackage::findOrFail($id);

        $validated = $request->validate($this->packageRules(true), $this->packageMessages());

        $package->update(
            $this->packageAttributes($validated) + ['isActive' => $validated['isActive']]
        );

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'packages'])
            ->with('success', 'Visa Package updated successfully.');
    }

    public function destroyPackage($id)
    {
        $package = UAEVisaPackage::findOrFail($id);
        $package->update(['isActive' => 0]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'packages'])
            ->with('success', 'Visa Package removed.');
    }

    // --- Prices Matrix CRUD ---
    public function storePriceRow(Request $request)
    {
        $validated = $request->validate([
            'visa_package_id' => 'required|exists:uae_visa_packages,id',
            'entry_type'      => 'required|string|max:100',
            'duration'        => 'required|string|max:100',
            'traveller_type'  => 'required|string|max:100',
            'price'           => 'required|numeric|min:0',
        ]);

        UAEVisaPrice::create([
            'visa_package_id' => $validated['visa_package_id'],
            'entry_type'      => $validated['entry_type'],
            'duration'        => $validated['duration'],
            'traveller_type'  => $validated['traveller_type'],
            'price'           => $validated['price'],
            'isActive'        => 1,
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Pricing row added successfully.');
    }

    public function updatePriceRow(Request $request, $id)
    {
        $price = UAEVisaPrice::findOrFail($id);

        $validated = $request->validate([
            'visa_package_id' => 'required|exists:uae_visa_packages,id',
            'entry_type'      => 'required|string|max:100',
            'duration'        => 'required|string|max:100',
            'traveller_type'  => 'required|string|max:100',
            'price'           => 'required|numeric|min:0',
            'isActive'        => 'required|boolean',
        ]);

        $price->update($validated);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Pricing row updated successfully.');
    }

    public function bulkUpdatePriceRow(Request $request)
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.entry_type' => 'required|string|max:100',
            'prices.*.duration' => 'required|string|max:100',
            'prices.*.traveller_type' => 'required|string|max:100',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.isActive' => 'required|boolean',
        ]);

        foreach ($validated['prices'] as $id => $data) {
            $price = UAEVisaPrice::findOrFail($id);
            $price->update([
                'entry_type'     => $data['entry_type'],
                'duration'       => $data['duration'],
                'traveller_type' => $data['traveller_type'],
                'price'          => $data['price'],
                'isActive'       => $data['isActive'],
            ]);
        }

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Pricing matrix updated successfully.');
    }

    public function destroyPriceRow($id)
    {
        $price = UAEVisaPrice::findOrFail($id);
        $price->delete();

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Pricing row deleted.');
    }
}
