<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeLetterMail;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birthday' => ['required', 'date'],
            'education' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'string', 'in:public,student,advocate,judge'],
            'residence_address' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile_number' => ['nullable', 'string', 'max:25'],
            'about' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8'],
            'country' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'receive_welcome_letter' => ['nullable', 'boolean'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must accept the Terms & Conditions and User Policy to create an account.',
            'birthday.required' => 'Birthday (Date of Birth) is required for account security and recovery.',
        ]);

        // Upload photo if provided
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('avatars', 'public');
        }

        // Generate unique 10-digit user code e.g. 0509092411
        $userCode = sprintf('%02d%02d%02d%04d', rand(1, 28), rand(1, 12), rand(10, 99), rand(1000, 9999));

        $roleSlug = $validated['role'];
        $isAutoApproved = in_array($roleSlug, ['public', 'student'], true);
        $status = $isAutoApproved ? 'active' : 'pending';
        $approvedAt = $isAutoApproved ? now() : null;

        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        $user = User::create([
            'user_code' => $userCode,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'dob' => $validated['birthday'],
            'education' => $validated['education'] ?? null,
            'role' => $roleSlug,
            'address' => $validated['residence_address'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'about' => $validated['about'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'profile_photo' => $photoPath,
            'status' => $status,
            'receive_welcome_letter' => $request->boolean('receive_welcome_letter'),
            'terms_accepted' => true,
            'approved_at' => $approvedAt,
        ]);

        // Assign Role
        $roleModel = Role::where('name', $roleSlug)->first();
        if ($roleModel) {
            $user->roles()->attach($roleModel->id);
        }

        ActivityLog::log('user_registered', "New user registered with role [{$roleSlug}]", $user);

        // Send welcome email if opted in
        if ($user->receive_welcome_letter) {
            try {
                Mail::to($user->email)->send(new WelcomeLetterMail($user));
            } catch (\Throwable $e) {
                Log::warning('Failed sending welcome email: ' . $e->getMessage());
            }
        }

        if ($status === 'pending') {
            return redirect()->route('login')->with('warning', "Account created successfully! Since you selected the role of " . ucfirst($roleSlug) . ", your account requires verification and approval by the Super Admin before you can log in.");
        }

        return redirect()->route('login')->with('success', 'Your account has been created successfully! You can now log in with your credentials.');
    }
}

