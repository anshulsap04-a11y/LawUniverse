<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List all notifications.
     */
    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        $roles = Role::all();
        return view('admin.notifications.index', compact('notifications', 'roles'));
    }

    /**
     * Post/broadcast notification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:system,announcement,alert,reminder',
            'target_audience' => 'required|in:all,role,user',
            'target_role' => 'nullable|string',
            'action_url' => 'nullable|url',
        ]);

        $notification = Notification::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'target_audience' => $validated['target_audience'],
            'target_role' => $validated['target_role'] ?? null,
            'action_url' => $validated['action_url'] ?? null,
            'sent_at' => now(),
        ]);

        // Resolve recipients
        $usersQuery = User::where('status', 'active');
        if ($validated['target_audience'] === 'role' && !empty($validated['target_role'])) {
            $usersQuery->where('role', $validated['target_role']);
        }

        $userIds = $usersQuery->pluck('id');

        $recipientsData = [];
        foreach ($userIds as $uid) {
            $recipientsData[] = [
                'notification_id' => $notification->id,
                'user_id' => $uid,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert in chunks
        foreach (array_chunk($recipientsData, 500) as $chunk) {
            NotificationRecipient::insert($chunk);
        }

        return back()->with('success', "Notification posted to " . count($recipientsData) . " users.");
    }
}
