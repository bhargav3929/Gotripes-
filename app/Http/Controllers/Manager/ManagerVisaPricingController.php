<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UAEVisaMaster;
use Illuminate\Http\Request;

class ManagerVisaPricingController extends Controller
{
    public function index()
    {
        $visas = UAEVisaMaster::where('isActive', 1)
            ->orderBy('UAEVisaDuration')
            ->get();

        $company = current_company();
        $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
        $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;

        return view('manager.visa-pricing.index', compact('visas', 'hotelFee', 'ticketFee'));
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
}
