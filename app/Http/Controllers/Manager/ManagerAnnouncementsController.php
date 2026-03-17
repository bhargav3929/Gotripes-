<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class ManagerAnnouncementsController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('isActive', 1)
                                     ->orderBy('createdDate', 'desc')
                                     ->paginate(10);
        return view('manager.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('manager.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'tagType' => 'required|in:none,breaking,trending,exclusive,alert',
        ]);

        Announcement::create([
            'description' => $request->input('description'),
            'tagType' => $request->input('tagType', 'none'),
            'AnnouncementImportance' => 0,
            'isActive' => 1,
            'createdBy' => session('manager_name', 'manager'),
            'createdDate' => now(),
            'modifiedby' => null,
            'modifiedDate' => null,
        ]);

        return redirect()->route('manager.announcements.index')
                       ->with('success', 'Announcement created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('manager.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'tagType' => 'required|in:none,breaking,trending,exclusive,alert',
        ]);

        $announcement->update([
            'description' => $request->input('description'),
            'tagType' => $request->input('tagType', 'none'),
            'modifiedBy' => session('manager_name', 'manager'),
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.announcements.index')
                       ->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->update([
            'isActive' => 0,
            'modifiedBy' => session('manager_name', 'manager'),
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.announcements.index')
                       ->with('success', 'Announcement removed successfully!');
    }
}
