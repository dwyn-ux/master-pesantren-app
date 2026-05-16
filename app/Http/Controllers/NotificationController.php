<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::forUser(Auth::id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(AppNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    public function markAllAsRead()
    {
        AppNotification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => AppNotification::forUser(Auth::id())->unread()->count(),
        ]);
    }
}
