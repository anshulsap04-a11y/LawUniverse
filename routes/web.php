<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RbacController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Advocate\DashboardController as AdvocateDashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Judge\DashboardController as JudgeDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Public\DashboardController as PublicDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Law Universe Portal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // 1. Standard Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // 2. Manual Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    // 3. Social OAuth (Google & Facebook)
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
    Route::get('/signup', [SocialAuthController::class, 'showRoleSelection'])->name('auth.social.role');
    Route::post('/signup/select-role', [SocialAuthController::class, 'completeRegistration'])->name('auth.social.role.submit');

    // 4. Instant Login with OTP
    Route::get('/auth/otp-login', [OtpLoginController::class, 'showRequestForm'])->name('auth.otp.request.form');
    Route::post('/auth/otp-login/send', [OtpLoginController::class, 'sendOtp'])->name('auth.otp.send');
    Route::get('/auth/otp-login/verify', [OtpLoginController::class, 'showVerifyForm'])->name('auth.otp.verify.form');
    Route::post('/auth/otp-login/verify', [OtpLoginController::class, 'verifyOtp'])->name('auth.otp.verify');

    // 5. Password Recovery Wizard (Dual Verification: Email + DOB)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// Logout (Authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Must be Authenticated & Active)
Route::middleware(['auth', 'active'])->group(function () {

    // -------------------------------------------------------------
    // Super Admin & Admin Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Module 1: Manage User Account
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::post('/bulk-trash', [UserController::class, 'bulkTrash'])->name('bulk_trash');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/approve', [UserController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [UserController::class, 'reject'])->name('reject');
            Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle_status');
        });

        // Module 8: Dynamic RBAC & Roles
        Route::prefix('rbac')->name('rbac.')->group(function () {
            Route::get('/', [RbacController::class, 'index'])->name('index');
            Route::post('/matrix', [RbacController::class, 'updateMatrix'])->name('matrix.update');
            Route::post('/roles', [RbacController::class, 'storeRole'])->name('roles.store');
            Route::get('/logs', [RbacController::class, 'auditLogs'])->name('logs');
        });
    });

    // -------------------------------------------------------------
    // Judge Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:judge'])->prefix('judge')->name('judge.')->group(function () {
        Route::get('/dashboard', [JudgeDashboardController::class, 'index'])->name('dashboard');
    });

    // -------------------------------------------------------------
    // Advocate Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:advocate'])->prefix('advocate')->name('advocate.')->group(function () {
        Route::get('/dashboard', [AdvocateDashboardController::class, 'index'])->name('dashboard');
    });

    // -------------------------------------------------------------
    // Student Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });

    // -------------------------------------------------------------
    // Public Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:public'])->prefix('public')->name('public.')->group(function () {
        Route::get('/dashboard', [PublicDashboardController::class, 'index'])->name('dashboard');
    });

    // -------------------------------------------------------------
    // Dynamic Module Protected Routes
    // -------------------------------------------------------------
    // Module 6: Manage AI Chatbot
    Route::middleware(['module.access:manage_ai_chatbot'])->prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/ask', [ChatbotController::class, 'ask'])->name('ask');
        Route::get('/history', [ChatbotController::class, 'history'])->name('history');
    });

    // Module 7: Manage Notification
    Route::middleware(['module.access:manage_notifications'])->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
    });
});
