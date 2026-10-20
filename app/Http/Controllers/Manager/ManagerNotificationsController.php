<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

/**
 * Mark-as-read actions for the manager topbar bell. The dropdown itself is
 * rendered server-side from auth()->user()->notifications in
 * layouts/manager.blade.php — there is no index/list endpoint to build.
 */
class ManagerNotificationsController extends Controller
{
    public function markRead(string $notification)
    {
        auth()->user()->notifications()->where('id', $notification)->first()?->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }
}
