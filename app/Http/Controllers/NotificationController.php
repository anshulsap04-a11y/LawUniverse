<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index');
    }

    public function create(): View
    {
        return view('notifications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'recipient_type' => ['nullable', 'string'],
        ]);

        ActivityLog::log('notification_sent', "Posted notification: {$validated['subject']}");

        return redirect()->route('notifications.index')->with('success', 'Notification posted successfully.');
    }
}
