<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use App\Models\UmrahDeparture;
use Illuminate\Http\Request;

class UmrahDepartureController extends Controller
{
    public function index($packageId)
    {
        $package = UmrahPackage::findOrFail($packageId);
        $departures = $package->departures()->orderBy('departure_date', 'asc')->get();

        return view('admin.umrah-packages.departures', compact('package', 'departures'));
    }

    public function store(Request $request, $packageId)
    {
        $package = UmrahPackage::findOrFail($packageId);

        $validated = $request->validate([
            'departure_date'  => 'required|date',
            'seats_available' => 'required|integer|min:0',
            'status'          => 'required|string|in:available,sold_out,inactive',
        ]);

        // Enforce departures are on Wednesdays
        $dayOfWeek = date('w', strtotime($validated['departure_date']));
        if ($dayOfWeek != 3) { // 3 = Wednesday
            return back()->withErrors(['departure_date' => 'Bus departures are only allowed on Wednesdays.'])->withInput();
        }

        $package->departures()->create([
            'departure_date'  => $validated['departure_date'],
            'seats_available' => $validated['seats_available'],
            'seats_booked'    => 0,
            'status'          => $validated['status'],
        ]);

        return redirect()->route('admin.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure date added successfully.');
    }

    public function update(Request $request, $packageId, $id)
    {
        $departure = UmrahDeparture::where('umrah_package_id', $packageId)->findOrFail($id);

        $validated = $request->validate([
            'seats_available' => 'required|integer|min:0',
            'status'          => 'required|string|in:available,sold_out,inactive',
        ]);

        $departure->update([
            'seats_available' => $validated['seats_available'],
            'status'          => $validated['status'],
        ]);

        return redirect()->route('admin.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure updated successfully.');
    }

    public function destroy($packageId, $id)
    {
        $departure = UmrahDeparture::where('umrah_package_id', $packageId)->findOrFail($id);
        $departure->delete();

        return redirect()->route('admin.umrah-packages.departures.index', $packageId)
            ->with('success', 'Departure removed.');
    }
}
