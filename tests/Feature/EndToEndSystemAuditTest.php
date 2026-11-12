<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AiConversation;
use App\Models\AiVendor;
use App\Models\BareAct;
use App\Models\Blog;
use App\Models\Coupon;
use App\Models\Examination;
use App\Models\ExaminationAttempt;
use App\Models\ExaminationCategory;
use App\Models\LandmarkJudgment;
use App\Models\LegalMaxim;
use App\Models\Module;
use App\Models\Note;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\OtpVerification;
use App\Models\Package;
use App\Models\Question;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndToEndSystemAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    /**
     * Audit 1: Complete Authentication Lifecycle
     */
    public function test_audit_complete_authentication_lifecycle(): void
    {
        // 1. Advocate Registration (Requires Admin Approval)
        $regData = [
            'first_name' => 'Meera',
            'last_name' => 'Shukla',
            'email' => 'meera.advocate@lawuniverse.com',
            'birthday' => '1992-06-15',
            'education' => 'LL.M. Constitutional Law',
            'role' => 'advocate',
            'password' => 'Advocate@Pass2026',
            'terms' => '1',
        ];

        $regResponse = $this->post(route('register'), $regData);
        $regResponse->assertRedirect(route('login'));
        $regResponse->assertSessionHas('warning');

        $advocate = User::where('email', 'meera.advocate@lawuniverse.com')->firstOrFail();
        $this->assertEquals('pending', $advocate->status);

        // Pending user cannot login
        $loginAttempt = $this->post(route('login'), [
            'email' => 'meera.advocate@lawuniverse.com',
            'password' => 'Advocate@Pass2026',
        ]);
        $loginAttempt->assertRedirect();
        $loginAttempt->assertSessionHas('warning');
        $this->assertGuest();

        // Super Admin approves the account
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $approveResponse = $this->actingAs($admin)->post(route('admin.users.approve', $advocate->id));
        $approveResponse->assertRedirect();
        $this->assertEquals('active', $advocate->fresh()->status);

        // Admin logs out so we are guest
        $this->post(route('logout'));
        $this->assertGuest();

        // Now advocate logs in successfully
        $authResponse = $this->from(route('login'))->post(route('login'), [
            'email' => 'meera.advocate@lawuniverse.com',
            'password' => 'Advocate@Pass2026',
        ]);
        $authResponse->assertRedirect(route('advocate.dashboard'));
        $this->assertAuthenticatedAs($advocate);

        // Advocate logs out
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        // 2. Dual-Factor Password Recovery (Email + DOB verification)
        $forgotResponse = $this->post(route('password.email'), [
            'email' => 'meera.advocate@lawuniverse.com',
            'dob' => '1992-06-15',
        ]);
        $forgotResponse->assertSessionHas('success');

        $resetTokenRecord = DB::table('password_reset_tokens')->where('email', 'meera.advocate@lawuniverse.com')->first();
        $this->assertNotNull($resetTokenRecord);

        // 3. Instant OTP Login
        $otpResponse = $this->post(route('auth.otp.send'), [
            'email' => 'meera.advocate@lawuniverse.com',
        ]);
        $otpResponse->assertRedirect(route('auth.otp.verify.form'));

        $otpRecord = OtpVerification::where('email', 'meera.advocate@lawuniverse.com')->latest()->first();
        $this->assertNotNull($otpRecord);

        $verifyOtp = $this->post(route('auth.otp.verify'), [
            'email' => 'meera.advocate@lawuniverse.com',
            'otp' => $otpRecord->otp_code,
        ]);
        $verifyOtp->assertRedirect(route('advocate.dashboard'));
        $this->assertAuthenticatedAs($advocate);
    }

    /**
     * Audit 2: Super Admin Sovereignty and Impersonation
     */
    public function test_audit_super_admin_sovereignty_and_impersonation(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);

        $studentRole = Role::where('name', 'student')->first();
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $student->roles()->attach($studentRole);

        // Super Admin accesses all dashboards
        $this->actingAs($superAdmin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('student.dashboard'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('advocate.dashboard'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('judge.dashboard'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('public.dashboard'))->assertStatus(200);

        // Impersonate Student
        $impersonateResponse = $this->actingAs($superAdmin)->post(route('admin.impersonate', $student->id));
        $impersonateResponse->assertRedirect(route('student.dashboard'));
        $this->assertEquals($student->id, auth()->id());
        $this->assertTrue(session()->has('impersonating_admin_id'));

        // Stop Impersonation
        $stopResponse = $this->post(route('impersonation.stop'));
        $stopResponse->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($superAdmin->id, auth()->id());
        $this->assertFalse(session()->has('impersonating_admin_id'));
    }

    /**
     * Audit 3: Examination Engine Workflow & Automated Scoring
     */
    public function test_audit_examination_engine_workflow(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $student->roles()->attach($studentRole);

        $category = ExaminationCategory::create([
            'name' => 'Judicial Prelims',
            'slug' => 'judicial-prelims',
        ]);

        $exam = Examination::create([
            'title' => 'Evidence Act Mock Test 2026',
            'exam_code' => 'EVID-2026',
            'duration_minutes' => 30,
            'total_marks' => 10.00,
            'passing_marks' => 5.00,
            'status' => 'published',
            'category_id' => $category->id,
        ]);

        $q1 = Question::create([
            'question_code' => 'Q-EVID-01',
            'question_title' => 'Under Indian Evidence Act, confession made to police officer is admissible?',
            'correct_answer' => 'B',
            'marks' => 5.00,
            'category_id' => $category->id,
            'options' => json_encode([
                ['id' => 'A', 'text' => 'Always admissible'],
                ['id' => 'B', 'text' => 'Inadmissible under Section 25'],
                ['id' => 'C', 'text' => 'Admissible if recorded on video'],
                ['id' => 'D', 'text' => 'Admissible with magistrate permission'],
            ]),
        ]);

        $q2 = Question::create([
            'question_code' => 'Q-EVID-02',
            'question_title' => 'Section 114A of Indian Evidence Act relates to presumption of absence of consent?',
            'correct_answer' => 'A',
            'marks' => 5.00,
            'category_id' => $category->id,
            'options' => json_encode([
                ['id' => 'A', 'text' => 'True / Valid Statutory Presumption'],
                ['id' => 'B', 'text' => 'False'],
            ]),
        ]);

        $exam->questions()->attach([
            $q1->id => ['order' => 1, 'marks' => 5.00],
            $q2->id => ['order' => 2, 'marks' => 5.00],
        ]);

        // Student starts exam attempt
        $startResponse = $this->actingAs($student)->post(route('exams.start', $exam->id));
        $startResponse->assertRedirect();

        $attempt = ExaminationAttempt::where('examination_id', $exam->id)
            ->where('user_id', $student->id)
            ->firstOrFail();

        // Student answers Q1 correctly and Q2 correctly
        $this->actingAs($student)->post(route('exams.save_answer', $attempt->id), [
            'question_id' => $q1->id,
            'selected_option' => 'B',
        ])->assertRedirect();

        $this->actingAs($student)->post(route('exams.save_answer', $attempt->id), [
            'question_id' => $q2->id,
            'selected_option' => 'A',
        ])->assertRedirect();

        // Submit exam
        $submitResponse = $this->actingAs($student)->post(route('exams.submit', $attempt->id));
        $submitResponse->assertRedirect(route('exams.result', $attempt->id));

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertEquals(10.00, $attempt->total_score);
        $this->assertEquals(100.00, $attempt->percentage);
        $this->assertTrue((bool)$attempt->is_passed);
        $this->assertEquals(2, $attempt->correct_count);
    }

    /**
     * Audit 4: Finance, Coupons & Subscriptions
     */
    public function test_audit_finance_and_subscriptions(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);

        $package = Package::create([
            'plan_code' => 'PLAN-ADV-YEAR',
            'title' => 'Advocate Pro Enterprise',
            'current_price' => 2400.00,
            'duration_days' => 365,
            'status' => 'active',
        ]);

        $coupon = Coupon::create([
            'coupon_code' => 'JUSTICE50',
            'coupon_name' => 'Justice 50% Off',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'max_uses' => 100,
            'used_count' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);

        // Toggle coupon active state
        $toggleResponse = $this->actingAs($admin)->post(route('admin.finance.coupons.toggle', $coupon->id));
        $toggleResponse->assertRedirect();
        $this->assertFalse((bool)$coupon->fresh()->is_active);

        // Reactivate
        $this->actingAs($admin)->post(route('admin.finance.coupons.toggle', $coupon->id));
        $this->assertTrue((bool)$coupon->fresh()->is_active);

        // Apply coupon calculation
        $discountAmount = $coupon->calculateDiscount(2400.00);
        $this->assertEquals(1200.00, $discountAmount);

        // Create subscription
        $advocate = User::factory()->create(['role' => 'advocate', 'status' => 'active']);
        $sub = Subscription::create([
            'user_id' => $advocate->id,
            'package_id' => $package->id,
            'start_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'active',
            'auto_renew' => true,
        ]);

        $this->assertTrue($advocate->fresh()->hasActiveSubscription());
    }

    /**
     * Audit 5: AI Provider Server Validation (Real probe without mock responses)
     */
    public function test_audit_ai_provider_server_validation(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);

        // Vendor with missing API key must fail validation cleanly
        $vendorEmpty = AiVendor::create([
            'vendor_name' => 'Unconfigured Provider',
            'api_base_url' => 'https://api.openai.com/v1',
            'api_key' => '',
            'model_name' => 'gpt-4o',
            'is_active' => true,
        ]);

        $validateResponse = $this->actingAs($admin)->post(route('admin.ai.vendors.validate', $vendorEmpty->id));
        $validateResponse->assertRedirect();
        $validateResponse->assertSessionHas('error');

        // Vendor with invalid key tested against real endpoint must report actual handshake error
        $vendorInvalid = AiVendor::create([
            'vendor_name' => 'OpenAI Legal Engine',
            'api_base_url' => 'https://api.openai.com/v1',
            'api_key' => 'sk-invalid-probe-key-12345',
            'model_name' => 'gpt-4o',
            'is_active' => true,
        ]);

        $validateInvalid = $this->actingAs($admin)->post(route('admin.ai.vendors.validate', $vendorInvalid->id));
        $validateInvalid->assertRedirect();
        // Since key is invalid, real HTTP probe fails and reports error
        $validateInvalid->assertSessionHas('error');
    }

    /**
     * Audit 6: Notification Delivery & Multi-Trash
     */
    public function test_audit_notification_delivery_and_multi_trash(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        // Post notification to all users
        $postResponse = $this->actingAs($admin)->post(route('admin.notifications.store'), [
            'subject' => 'Supreme Court Winter Vacation Circular',
            'message' => 'The registry will observe winter recess with vacation benches operating.',
            'recipient_type' => 'all',
        ]);
        $postResponse->assertRedirect();

        $notif = Notification::latest()->firstOrFail();
        $this->assertEquals('Supreme Court Winter Vacation Circular', $notif->subject);

        // Recipient record created for student
        $recipient = NotificationRecipient::where('notification_id', $notif->id)
            ->where('user_id', $student->id)
            ->firstOrFail();

        $this->assertFalse((bool)$recipient->is_read);

        // Student marks notification as read
        $this->actingAs($student)->get(route('notifications.read', $recipient->id));
        $this->assertTrue((bool)$recipient->fresh()->is_read);

        // Admin bulk trash notifications
        $this->actingAs($admin)->post(route('admin.notifications.bulk_trash'), [
            'ids' => [$notif->id],
        ])->assertRedirect();

        $this->assertSoftDeleted('notifications', ['id' => $notif->id]);
    }

    /**
     * Audit 7: Security Audit Logs & IDOR Protection
     */
    public function test_audit_security_audit_logs_and_idor_protection(): void
    {
        $studentA = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $studentB = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $exam = Examination::create([
            'title' => 'Jurisprudence Test',
            'exam_code' => 'JUR-001',
            'total_marks' => 50,
            'passing_marks' => 20,
            'duration_minutes' => 30,
            'status' => 'published',
        ]);

        $attemptA = ExaminationAttempt::create([
            'examination_id' => $exam->id,
            'user_id' => $studentA->id,
            'start_time' => now(),
            'total_score' => 45.00,
            'percentage' => 90.00,
            'is_passed' => true,
            'status' => 'completed',
        ]);

        // Student A can access their result
        $this->actingAs($studentA)->get(route('exams.result', $attemptA->id))->assertStatus(200);

        // Student B attempting to access Student A's result is blocked (IDOR Protection)
        $this->actingAs($studentB)->get(route('exams.result', $attemptA->id))->assertStatus(403);

        // Check that activity log records events
        ActivityLog::log('security_check', 'End to end security audit execution');
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'security_check',
        ]);
    }
}
