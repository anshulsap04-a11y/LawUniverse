<?php

use App\Http\Controllers\Admin\AiEngineController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExaminationController as AdminExaminationController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\RbacController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Advocate\DashboardController as AdvocateDashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ExamAttemptController;
use App\Http\Controllers\HomeController;
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

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/resources', [HomeController::class, 'resources'])->name('public.resources');
Route::get('/bare-acts', [HomeController::class, 'bareActs'])->name('public.bare-acts');
Route::get('/legal-maxims', [HomeController::class, 'legalMaxims'])->name('public.legal-maxims');
Route::get('/landmark-judgments', [HomeController::class, 'landmarkJudgments'])->name('public.landmark-judgments');
Route::get('/blogs', [HomeController::class, 'blogs'])->name('public.blogs');
Route::get('/blogs/{slug}', [HomeController::class, 'blogDetail'])->name('public.blogs.show');

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

// Impersonation Exit (Works even if impersonated user is not admin)
Route::post('/impersonation/stop', [ImpersonationController::class, 'stop'])->name('impersonation.stop')->middleware('auth');

// Logout (Authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Must be Authenticated & Active)
Route::middleware(['auth', 'active'])->group(function () {

    // -------------------------------------------------------------
    // Super Admin & Admin Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Impersonation
        Route::post('/impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');

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

        // Module 2: Manage Resource
        Route::prefix('resources')->name('resources.')->group(function () {
            Route::get('/', [AdminResourceController::class, 'index'])->name('index');
            Route::get('/create', [AdminResourceController::class, 'create'])->name('create');
            Route::post('/', [AdminResourceController::class, 'store'])->name('store');
            Route::post('/{resource}/clone', [AdminResourceController::class, 'clone'])->name('clone');
            Route::delete('/{resource}', [AdminResourceController::class, 'destroy'])->name('destroy');
            Route::get('/bare-acts', [AdminResourceController::class, 'bareActs'])->name('bare_acts');
            Route::post('/bare-acts', [AdminResourceController::class, 'storeBareAct'])->name('bare_acts.store');
            Route::get('/legal-maxims', [AdminResourceController::class, 'legalMaxims'])->name('legal_maxims');
            Route::post('/legal-maxims', [AdminResourceController::class, 'storeLegalMaxim'])->name('legal_maxims.store');
            Route::get('/landmark-judgments', [AdminResourceController::class, 'landmarkJudgments'])->name('landmark_judgments');
            Route::post('/landmark-judgments', [AdminResourceController::class, 'storeLandmarkJudgment'])->name('landmark_judgments.store');
            Route::get('/notes', [AdminResourceController::class, 'notes'])->name('notes');
            Route::post('/notes', [AdminResourceController::class, 'storeNote'])->name('notes.store');
        });

        // Module 3: Manage Examination
        Route::prefix('examinations')->name('examinations.')->group(function () {
            Route::get('/', [AdminExaminationController::class, 'index'])->name('index');
            Route::get('/create', [AdminExaminationController::class, 'create'])->name('create');
            Route::post('/', [AdminExaminationController::class, 'store'])->name('store');
            Route::get('/questions', [AdminExaminationController::class, 'questions'])->name('questions');
            Route::post('/questions', [AdminExaminationController::class, 'storeQuestion'])->name('questions.store');
            Route::get('/previous-years', [AdminExaminationController::class, 'previousYearPapers'])->name('previous_years');
            Route::post('/previous-years', [AdminExaminationController::class, 'storePreviousYearPaper'])->name('previous_years.store');
            Route::get('/categories-labels', [AdminExaminationController::class, 'categoriesAndLabels'])->name('categories_labels');
            Route::post('/categories', [AdminExaminationController::class, 'storeCategory'])->name('categories.store');
            Route::post('/labels', [AdminExaminationController::class, 'storeLabel'])->name('labels.store');
        });

        // Module 4: Manage Blogging
        Route::prefix('blogs')->name('blogs.')->group(function () {
            Route::get('/', [AdminBlogController::class, 'index'])->name('index');
            Route::get('/create', [AdminBlogController::class, 'create'])->name('create');
            Route::post('/', [AdminBlogController::class, 'store'])->name('store');
            Route::get('/statistics', [AdminBlogController::class, 'statistics'])->name('statistics');
            Route::delete('/{blog}', [AdminBlogController::class, 'destroy'])->name('destroy');
        });

        // Module 5: Manage Finance
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/statistics', [AdminFinanceController::class, 'statistics'])->name('statistics');
            Route::get('/packages', [AdminFinanceController::class, 'packages'])->name('packages');
            Route::post('/packages', [AdminFinanceController::class, 'storePackage'])->name('packages.store');
            Route::get('/features', [AdminFinanceController::class, 'features'])->name('features');
            Route::post('/features', [AdminFinanceController::class, 'storeFeature'])->name('features.store');
            Route::get('/transactions', [AdminFinanceController::class, 'transactions'])->name('transactions');
            Route::get('/subscriptions', [AdminFinanceController::class, 'subscriptions'])->name('subscriptions');
            Route::get('/coupons', [AdminFinanceController::class, 'coupons'])->name('coupons');
            Route::post('/coupons', [AdminFinanceController::class, 'storeCoupon'])->name('coupons.store');
        });

        // Module 6: Manage AI Chatbot (Vendor API Admin)
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/', [AiEngineController::class, 'index'])->name('index');
            Route::post('/vendors', [AiEngineController::class, 'storeVendor'])->name('vendors.store');
            Route::post('/vendors/{vendor}/toggle', [AiEngineController::class, 'toggleVendor'])->name('vendors.toggle');
            Route::get('/conversations', [AiEngineController::class, 'conversations'])->name('conversations');
        });

        // Module 7: Manage Notification (Admin Broadcast)
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::post('/', [AdminNotificationController::class, 'store'])->name('store');
        });

        // Module 8: Dynamic RBAC & Roles
        Route::prefix('rbac')->name('rbac.')->group(function () {
            Route::get('/', [RbacController::class, 'index'])->name('index');
            Route::post('/matrix', [RbacController::class, 'updateMatrix'])->name('matrix.update');
            Route::post('/roles', [RbacController::class, 'storeRole'])->name('roles.store');
            Route::get('/logs', [RbacController::class, 'auditLogs'])->name('logs');
        });

        // System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/', [SettingController::class, 'update'])->name('update');
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
    // Public User Control Panel
    // -------------------------------------------------------------
    Route::middleware(['role:public'])->prefix('public')->name('public.')->group(function () {
        Route::get('/dashboard', [PublicDashboardController::class, 'index'])->name('dashboard');
    });

    // -------------------------------------------------------------
    // Examination Engine (Taking & Scoring Exams)
    // -------------------------------------------------------------
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::post('/start/{exam}', [ExamAttemptController::class, 'start'])->name('start');
        Route::get('/attempt/{attempt}', [ExamAttemptController::class, 'attempt'])->name('attempt');
        Route::post('/attempt/{attempt}/answer', [ExamAttemptController::class, 'saveAnswer'])->name('save_answer');
        Route::post('/attempt/{attempt}/submit', [ExamAttemptController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [ExamAttemptController::class, 'result'])->name('result');
    });

    // -------------------------------------------------------------
    // AI Chatbot (Interactive legal assistant for users)
    // -------------------------------------------------------------
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/ask', [ChatbotController::class, 'ask'])->name('ask');
        Route::post('/ask', [ChatbotController::class, 'submit'])->name('submit');
        Route::get('/history', [ChatbotController::class, 'history'])->name('history');
    });

    // -------------------------------------------------------------
    // User Notification Center
    // -------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read/{recipient}', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read_all');
    });
});
