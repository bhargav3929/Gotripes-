<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use App\Models\UmrahDeparture;
use Illuminate\Http\Request;

class ManagerUmrahDepartureController extends Controller
{
    public function index($packageId)
    {
        $package = UmrahPackage::findOrFail($packageId);
        $departures = $package->departures()->orderBy('departure_date', 'asc')->get();

        return view('manager.umrah-packages.departures', compact('package', 'departures'));
    }

    public function store(Request $request, $packageId)
    {
        $package = UmrahPackage::findOrFail($packageId);

        $validated = $request->validate([
            'departure_date'  => 'required|date',
            'seats_total'     => 'required|integer|min:0',
            'booking_cutoff'  => 'nullable|date',
            'status'          => 'required|string|in:available,sold_out,inactive',
        ]);

        if ($package->category === 'bus') {
            $dayOfWeek = date('w', strtotime($validated['departure_date']));
            if ($dayOfWeek != 3) {
                return back()->withErrors(['departure_date' => 'Bus departures are only allowed on Wednesdays.'])->withInput();
            }
        }

        $package->departures()->create([
            'departure_date'  => $validated['departure_date'],
            'seats_total'     => $validated['seats_total'],
            'seats_available' => $validated['seats_total'],
            'seats_booked'    => 0,
            'booking_cutoff'  => $validated['booking_cutoff'] ?? null,
            'status'          => $validated['status'],
        ]);

        return redirect()->route('manager.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure date added successfully.');
    }

    public function update(Request $request, $packageId, $id)
    {
        $departure = UmrahDeparture::where('umrah_package_id', $packageId)->findOrFail($id);

        $validated = $request->validate([
            'seats_total'     => 'required|integer|min:0',
            'booking_cutoff'  => 'nullable|date',
            'status'          => 'required|string|in:available,sold_out,inactive',
        ]);

        // seats_available always mirrors gross capacity (seats_total) — every
        // reader (dashboard totals, checkout's seat check, the departures
        // list) computes remaining seats itself as seats_available -
        // seats_booked. Writing an already-net value here made those readers
        // double-subtract seats_booked after every edit to a departure that
        // already had bookings.
        $remaining = max(0, $validated['seats_total'] - $departure->seats_booked);
        $status = $validated['status'];
        if ($remaining == 0 && $status == 'available') {
            $status = 'sold_out';
        }

        $departure->update([
            'seats_total'     => $validated['seats_total'],
            'seats_available' => $validated['seats_total'],
            'booking_cutoff'  => $validated['booking_cutoff'] ?? null,
            'status'          => $status,
        ]);

        return redirect()->route('manager.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure updated successfully.');
    }

    public function destroy($packageId, $id)
    {
        $departure = UmrahDeparture::where('umrah_package_id', $packageId)->findOrFail($id);
        $departure->delete();

        return redirect()->route('manager.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure removed.');
    }
}
