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
    // Mirrors the option lists in the Pricing tab's dropdowns — kept here so
    // storePackage() can pre-generate the full matrix for a new package.
    private const ENTRY_TYPES = ['Single Entry', 'Multiple Entry'];
    private const DURATIONS = ['30 Days', '60 Days'];
    private const TRAVELLER_TYPES = ['Adult', 'Child', 'Infant'];

    public function index()
    {
        $visas = UAEVisaMaster::where('isActive', 1)
            ->orderBy('UAEVisaDuration')
            ->get();

        $emirates = Emirates::orderBy('emiratesName')->get();
        $packages = UAEVisaPackage::with('emirate')->orderBy('name')->get();
        $prices = UAEVisaPrice::with('package')->get();

        $company = current_company();
        $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
        $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;
        $supplierEmail = $company?->getSetting('visa_supplier_email', '') ?? '';
        // Sharjah refundable security deposit per applicant. Defaults to 0 (no
        // deposit) until a manager configures an amount — the storefront then
        // shows a generic message and charges nothing.
        $sharjahDeposit = $company?->getSetting('visa_sharjah_deposit', 0);
        $sharjahDeposit = is_numeric($sharjahDeposit) ? (float) $sharjahDeposit : 0;
        // Non-refundable admin/processing fee deducted from the deposit when it is
        // returned. Defaults to 0, in which case the whole deposit is refundable
        // and the fee row is hidden everywhere on the frontend.
        $sharjahAdminFee = $company?->getSetting('visa_sharjah_deposit_admin_fee', 0);
        $sharjahAdminFee = is_numeric($sharjahAdminFee) ? (float) $sharjahAdminFee : 0;

        // Global markup applied to every Fluxir e-Visa (the /e-visa storefront).
        $evisaMarkup = EvisaSetting::markupPercent();

        return view('manager.visa-pricing.index', compact('visas', 'emirates', 'packages', 'prices', 'hotelFee', 'ticketFee', 'evisaMarkup', 'supplierEmail', 'sharjahDeposit', 'sharjahAdminFee'));
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

    public function updateServiceFees(Request $request)
    {
        $validated = $request->validate([
            'visa_hotel_booking_fee'  => 'required|numeric|min:0',
            'visa_ticket_booking_fee' => 'required|numeric|min:0',
            'visa_supplier_email'     => 'nullable|email|max:255',
            'visa_sharjah_deposit'    => 'nullable|numeric|min:0',
            'visa_sharjah_deposit_admin_fee' => 'nullable|numeric|min:0|lte:visa_sharjah_deposit',
        ], [
            'visa_sharjah_deposit_admin_fee.lte' => 'The admin/processing fee cannot be greater than the security deposit.',
        ]);

        $company = current_company();

        $settings = $company->settings ?? [];
        $settings['visa_hotel_booking_fee']  = (float) $validated['visa_hotel_booking_fee'];
        $settings['visa_ticket_booking_fee'] = (float) $validated['visa_ticket_booking_fee'];
        $settings['visa_supplier_email']     = $validated['visa_supplier_email'] ? trim($validated['visa_supplier_email']) : null;
        // Blank or 0 = no deposit: the storefront shows a generic message and
        // charges nothing. Any positive amount is shown and charged per applicant.
        $settings['visa_sharjah_deposit']    = ($validated['visa_sharjah_deposit'] ?? '') !== ''
            ? (float) $validated['visa_sharjah_deposit']
            : 0.0;
        // Deducted from the deposit at refund time. Blank or 0 = the full deposit
        // is refundable and the fee row is hidden on the frontend.
        $settings['visa_sharjah_deposit_admin_fee'] = ($validated['visa_sharjah_deposit_admin_fee'] ?? '') !== ''
            ? (float) $validated['visa_sharjah_deposit_admin_fee']
            : 0.0;
        $company->settings = $settings;
        $company->save();

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'settings'])
            ->with('success', 'Visa settings updated.');
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
    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'emirates_id' => 'required|exists:tbl_emirates,emiratesID',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $package = UAEVisaPackage::create([
            'emirates_id' => $validated['emirates_id'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'isActive'    => 1,
        ]);

        // Pre-fill the whole pricing matrix so the manager only has to type in
        // prices on the Pricing tab, instead of adding every row by hand.
        // Left inactive at AED 0 until a real price is set and activated —
        // this keeps unpriced rows from ever appearing on the storefront.
        foreach (self::ENTRY_TYPES as $entryType) {
            foreach (self::DURATIONS as $duration) {
                foreach (self::TRAVELLER_TYPES as $travellerType) {
                    UAEVisaPrice::create([
                        'visa_package_id' => $package->id,
                        'entry_type'      => $entryType,
                        'duration'        => $duration,
                        'traveller_type'  => $travellerType,
                        'price'           => 0,
                        'isActive'        => 0,
                    ]);
                }
            }
        }

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'pricing'])
            ->with('success', 'Visa Package added. Fill in prices below and mark rows Active to publish them.');
    }

    public function updatePackage(Request $request, $id)
    {
        $package = UAEVisaPackage::findOrFail($id);

        $validated = $request->validate([
            'emirates_id' => 'required|exists:tbl_emirates,emiratesID',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'isActive'    => 'required|boolean',
        ]);

        $package->update($validated);

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
            'nationality'     => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
        ]);

        $nationality = $validated['nationality'] ?? null;

        // Every package already gets the full matrix pre-filled on creation,
        // so a manual add here is either a nationality-specific override or a
        // genuine duplicate. Block the duplicate case — two rows for the same
        // combination leaves the storefront with no way to know which price
        // is the real one.
        $duplicate = UAEVisaPrice::where('visa_package_id', $validated['visa_package_id'])
            ->where('entry_type', $validated['entry_type'])
            ->where('duration', $validated['duration'])
            ->where('traveller_type', $validated['traveller_type'])
            ->where('nationality', $nationality)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'price' => 'A price row already exists for this package, entry type, duration, traveller type'
                    . ($nationality ? " and nationality ($nationality)" : ' with no nationality set')
                    . '. Edit it in the matrix below instead.',
            ]);
        }

        UAEVisaPrice::create([
            'visa_package_id' => $validated['visa_package_id'],
            'entry_type'      => $validated['entry_type'],
            'duration'        => $validated['duration'],
            'traveller_type'  => $validated['traveller_type'],
            'nationality'     => $nationality,
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
            'nationality'     => 'nullable|string|max:100',
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
            'prices.*.nationality' => 'nullable|string|max:100',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.isActive' => 'required|boolean',
        ]);

        foreach ($validated['prices'] as $id => $data) {
            $price = UAEVisaPrice::findOrFail($id);
            $price->update([
                'entry_type'     => $data['entry_type'],
                'duration'       => $data['duration'],
                'traveller_type' => $data['traveller_type'],
                'nationality'    => $data['nationality'] ?? null,
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
