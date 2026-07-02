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
    public function index()
    {
        $visas = UAEVisaMaster::where('isActive', 1)
            ->orderBy('UAEVisaDuration')
            ->get();

        $emirates = Emirates::orderBy('emiratesName')->get();
        $packages = UAEVisaPackage::with('emirate')->where('isActive', 1)->orderBy('name')->get();
        $prices = UAEVisaPrice::with('package')->where('isActive', 1)->get();

        $company = current_company();
        $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
        $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;

        // Global markup applied to every Fluxir e-Visa (the /e-visa storefront).
        $evisaMarkup = EvisaSetting::markupPercent();

        return view('manager.visa-pricing.index', compact('visas', 'emirates', 'packages', 'prices', 'hotelFee', 'ticketFee', 'evisaMarkup'));
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
        ]);

        $company = current_company();

        $settings = $company->settings ?? [];
        $settings['visa_hotel_booking_fee']  = (float) $validated['visa_hotel_booking_fee'];
        $settings['visa_ticket_booking_fee'] = (float) $validated['visa_ticket_booking_fee'];
        $company->settings = $settings;
        $company->save();

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Service fees updated.');
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        UAEVisaPackage::create([
            'emirates_id' => $validated['emirates_id'],
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'isActive'    => 1,
        ]);

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Visa Package added successfully.');
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

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Visa Package updated successfully.');
    }

    public function destroyPackage($id)
    {
        $package = UAEVisaPackage::findOrFail($id);
        $package->update(['isActive' => 0]);

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
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

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Pricing row updated successfully.');
    }

    public function destroyPriceRow($id)
    {
        $price = UAEVisaPrice::findOrFail($id);
        $price->delete();

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Pricing row deleted.');
    }
}
