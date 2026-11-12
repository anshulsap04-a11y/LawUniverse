<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeLetterMail;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'approver'])->latest();

        // Search text field (matches name, email, user_code, phone)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        // Filter by Date Added (mm/dd/yyyy or yyyy-mm-dd)
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->date)->format('Y-m-d');
                $query->whereDate('created_at', $date);
            } catch (\Throwable $e) {
                // ignore invalid format
            }
        }

        // Filter by Status (Activated / Deactivated / Pending / Rejected)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by User Type / Role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthday' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'about' => ['nullable', 'string', 'max:1000'],
            'mobile_number' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'residence_address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8'],
            'country' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'status' => ['nullable', 'in:active,pending,deactivated'],
            'receive_welcome_letter' => ['nullable', 'boolean'],
            'enable_2fa' => ['nullable', 'boolean'],
            'enable_instant_otp' => ['nullable', 'boolean'],
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('avatars', 'public');
        }

        $userCode = sprintf('%02d%02d%02d%04d', rand(1, 28), rand(1, 12), rand(10, 99), rand(1000, 9999));
        $status = $validated['status'] ?? ($validated['role'] === 'advocate' || $validated['role'] === 'judge' ? 'pending' : 'active');
        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        $user = User::create([
            'user_code' => $userCode,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'dob' => $validated['birthday'] ?? null,
            'education' => $validated['education'] ?? null,
            'role' => $validated['role'],
            'address' => $validated['residence_address'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'about' => $validated['about'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'profile_photo' => $photoPath,
            'status' => $status,
            'receive_welcome_letter' => $request->boolean('receive_welcome_letter'),
            'enable_2fa' => $request->boolean('enable_2fa'),
            'enable_instant_otp' => $request->boolean('enable_instant_otp', true),
            'approved_at' => $status === 'active' ? now() : null,
            'approved_by' => $status === 'active' ? auth()->id() : null,
        ]);

        $roleModel = Role::where('name', $validated['role'])->first();
        if ($roleModel) {
            $user->roles()->attach($roleModel->id);
        }

        ActivityLog::log('admin_created_user', "Admin created user [{$user->email}] with role [{$user->role}]");

        if ($user->receive_welcome_letter) {
            try {
                Mail::to($user->email)->send(new WelcomeLetterMail($user));
            } catch (\Throwable $e) {
                Log::warning('Failed sending welcome email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.users.index')->with('success', "User account [{$user->name}] created successfully with User ID {$user->user_code}.");
    }

    public function show(string|int $id): View
    {
        $user = User::withTrashed()->with(['roles', 'approver'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit(string|int $id): View
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthday' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', 'in:active,pending,deactivated,rejected'],
            'mobile_number' => ['nullable', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'residence_address' => ['nullable', 'string', 'max:500'],
            'about' => ['nullable', 'string', 'max:1000'],
            'country' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'enable_2fa' => ['nullable', 'boolean'],
            'enable_instant_otp' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('avatars', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['name'] = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $validated['dob'] = $validated['birthday'] ?? $user->dob;
        $validated['address'] = $validated['residence_address'] ?? $user->address;
        $validated['enable_2fa'] = $request->boolean('enable_2fa');
        $validated['enable_instant_otp'] = $request->boolean('enable_instant_otp');

        // Approval bookkeeping
        if ($validated['status'] === 'active' && $user->status !== 'active') {
            $validated['approved_at'] = now();
            $validated['approved_by'] = auth()->id();
        }

        $user->update($validated);

        // Sync role
        $roleModel = Role::where('name', $validated['role'])->first();
        if ($roleModel) {
            $user->roles()->sync([$roleModel->id]);
        }

        ActivityLog::log('admin_updated_user', "Admin updated user [{$user->email}]");

        return redirect()->route('admin.users.index')->with('success', "User [{$user->name}] updated successfully.");
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super Admin accounts cannot be trashed or deleted.');
        }

        $userName = $user->name;
        $user->delete(); // Soft delete per specs

        ActivityLog::log('admin_trashed_user', "Admin trashed user [{$userName}]");

        return redirect()->route('admin.users.index')->with('success', "User [{$userName}] moved to trash successfully.");
    }

    public function bulkTrash(Request $request): RedirectResponse
    {
        $ids = $request->input('selected_ids', []);

        if (empty($ids) || !is_array($ids)) {
            return back()->with('warning', 'Please select at least one user to perform Multi-Trash.');
        }

        // Prevent trashing super admin accounts or current logged in admin
        $usersToTrash = User::whereIn('id', $ids)
            ->where('role', '!=', 'super_admin')
            ->where('id', '!=', auth()->id())
            ->get();

        $count = 0;
        foreach ($usersToTrash as $u) {
            $u->delete();
            $count++;
        }

        ActivityLog::log('admin_multi_trash', "Multi-Trash deleted {$count} users");

        return redirect()->route('admin.users.index')->with('success', "Successfully deleted {$count} selected user account(s).");
    }

    public function approve(string|int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        ActivityLog::log('admin_approved_user', "Approved role for user [{$user->email}]", $user);

        return back()->with('success', "Account for [{$user->name}] as " . ucfirst($user->role) . " has been approved successfully.");
    }

    public function reject(string|int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => 'rejected',
        ]);

        ActivityLog::log('admin_rejected_user', "Rejected account for user [{$user->email}]", $user);

        return back()->with('warning', "Account for [{$user->name}] has been rejected.");
    }

    public function toggleStatus(string|int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot change Super Admin account status.');
        }

        $newStatus = $user->status === 'active' ? 'deactivated' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::log('admin_toggled_user_status', "User [{$user->email}] status changed to {$newStatus}");

        return back()->with('success', "User [{$user->name}] status changed to {$newStatus}.");
    }
}

