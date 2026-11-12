<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * View user's inbox / notifications list.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $notifications = NotificationRecipient::where('user_id', $user->id)
            ->with('notification')
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(NotificationRecipient $recipient): RedirectResponse
    {
        if ($recipient->user_id === auth()->id()) {
            $recipient->update(['is_read' => true, 'read_at' => now()]);
        }

        if ($recipient->notification && $recipient->notification->action_url) {
            return redirect($recipient->notification->action_url);
        }

        return back();
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(): RedirectResponse
    {
        NotificationRecipient::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
