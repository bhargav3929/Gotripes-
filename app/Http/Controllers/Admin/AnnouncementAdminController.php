<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AnnouncementAdminController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('isActive', 1)->orderBy('createdDate', 'desc')->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'flagPath' => 'nullable|file|mimetypes:image/svg+xml|max:2048'
        ]);

        $flagPath = ''; // Default to empty string instead of null
        
        if ($request->hasFile('flagPath')) {
            $file = $request->file('flagPath');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('assets/flags'); // Direct to public/assets/flags

            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Move file to public/assets/flags
            $file->move($destinationPath, $filename);
            $flagPath = 'assets/flags/' . $filename; // Relative path from public folder
        }

        Announcement::create([
            'description' => $request->description,
            'isActive' => 1,
            'AnnouncementImportance' => $request->has('announcementImportance') ? 1 : 0,
            'flagImgPath' => $flagPath, // Always has a value (empty string or path)
            'createdBy' => auth()->user()->name,
            'createdDate' => now(),
            'modifiedBy' => null,
            'modifiedDate' => null
        ]);

        return redirect()->route('admin.announcements.index')
                       ->with('success', 'Announcement created successfully!');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'flagPath' => 'nullable|file|mimetypes:image/svg+xml|max:2048'
        ]);

        $flagPath = $announcement->flagImgPath; // Keep existing path as default
        
        if ($request->hasFile('flagPath')) {
            // Delete old file if exists
            if ($announcement->flagImgPath && file_exists(public_path($announcement->flagImgPath))) {
                unlink(public_path($announcement->flagImgPath));
            }

            $file = $request->file('flagPath');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('assets/flags'); // Direct to public/assets/flags

            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Move file to public/assets/flags
            $file->move($destinationPath, $filename);
            $flagPath = 'assets/flags/' . $filename; // Relative path from public folder
        }

        $announcement->update([
            'description' => $request->description,
            'AnnouncementImportance' => $request->has('announcementImportance') ? 1 : 0,
            'flagImgPath' => $flagPath ?? '', // Ensure never null
            'modifiedBy' => auth()->user()->name,
            'modifiedDate' => now()
        ]);

        return redirect()->route('admin.announcements.index')
                       ->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        // Delete associated file if exists
        if ($announcement->flagImgPath && file_exists(public_path($announcement->flagImgPath))) {
            unlink(public_path($announcement->flagImgPath));
        }
        
        $announcement->update([
            'isActive' => 0,
            'modifiedBy' => auth()->user()->name,
            'modifiedDate' => now()
        ]);

        return redirect()->route('admin.announcements.index')
                       ->with('success', 'Announcement deleted successfully!');
    }
}
