<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('subject', 'like', "%{$s}%")
                  ->orWhere('subject_code', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $notifications = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('admin.notifications.index', compact('notifications', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,role,individual',
            'target_roles' => 'nullable|array',
            'status' => 'nullable|in:published,draft,scheduled',
        ]);

        $notification = Notification::create([
            'subject_code' => 'NOTIF-' . strtoupper(Str::random(6)),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'recipient_type' => $validated['recipient_type'],
            'target_roles' => $validated['target_roles'] ?? null,
            'post_date' => now(),
            'status' => $validated['status'] ?? 'published',
            'created_by' => auth()->id(),
        ]);

        $usersQuery = User::where('status', 'active');
        if ($validated['recipient_type'] === 'role' && !empty($validated['target_roles'])) {
            $usersQuery->whereIn('role', $validated['target_roles']);
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

        foreach (array_chunk($recipientsData, 500) as $chunk) {
            NotificationRecipient::insert($chunk);
        }

        return back()->with('success', "Notification broadcasted to " . count($recipientsData) . " users.");
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function bulkTrash(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);

        Notification::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' notifications deleted.');
    }
}
