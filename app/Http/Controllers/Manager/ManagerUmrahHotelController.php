<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahHotel;
use Illuminate\Http\Request;

class ManagerUmrahHotelController extends Controller
{
    public function index()
    {
        $hotels = UmrahHotel::orderBy('created_at', 'desc')->paginate(20);
        return view('manager.umrah.hotels.index', compact('hotels'));
    }

    public function create()
    {
        return view('manager.umrah.hotels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'star_rating' => 'required|integer|min:1|max:7',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'images']);
        $data['isActive'] = $request->has('isActive') ? 1 : 0;
        
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('umrah/hotels', 'public');
                $images[] = '/storage/' . $path;
            }
            $data['images'] = $images;
        }

        if ($request->has('amenities')) {
            $data['amenities'] = is_array($request->amenities) ? $request->amenities : array_map('trim', explode(',', $request->amenities));
        }

        UmrahHotel::create($data);

        return redirect()->route('manager.umrah.hotels.index')->with('success', 'Hotel created successfully.');
    }

    public function edit(UmrahHotel $hotel)
    {
        return view('manager.umrah.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, UmrahHotel $hotel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'star_rating' => 'required|integer|min:1|max:7',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'images']);
        $data['isActive'] = $request->has('isActive') ? 1 : 0;
        
        if ($request->hasFile('images')) {
            $images = $hotel->images ?? [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('umrah/hotels', 'public');
                $images[] = '/storage/' . $path;
            }
            $data['images'] = $images;
        }

        if ($request->has('amenities')) {
            $data['amenities'] = is_array($request->amenities) ? $request->amenities : array_map('trim', explode(',', $request->amenities));
        }

        $hotel->update($data);

        return redirect()->route('manager.umrah.hotels.index')->with('success', 'Hotel updated successfully.');
    }

    public function destroy(UmrahHotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('manager.umrah.hotels.index')->with('success', 'Hotel deleted successfully.');
    }
}
