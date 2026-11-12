<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\Examination;
use App\Models\ExaminationAttempt;
use App\Models\Module;
use App\Models\Package;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardsAndScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_public_user_can_access_public_dashboard_only(): void
    {
        $publicRole = Role::where('name', 'public')->first();
        $user = User::factory()->create([
            'role' => 'public',
            'status' => 'active',
        ]);
        $user->roles()->attach($publicRole);

        $response = $this->actingAs($user)->get(route('public.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Public Legal Portal');
        $response->assertSee('Citizen Rights, Free Bare Acts');

        // Cannot access student, advocate, judge dashboards
        $this->actingAs($user)->get(route('student.dashboard'))->assertStatus(403);
        $this->actingAs($user)->get(route('advocate.dashboard'))->assertStatus(403);
        $this->actingAs($user)->get(route('judge.dashboard'))->assertStatus(403);
    }

    public function test_student_can_access_student_dashboard_and_see_own_metrics(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);
        $student->roles()->attach($studentRole);

        $exam = Examination::create([
            'title' => 'Constitutional Law Mock Test',
            'exam_code' => 'CONST-101',
            'status' => 'published',
            'duration_minutes' => 60,
            'total_marks' => 100,
            'passing_marks' => 40,
        ]);

        $attempt = ExaminationAttempt::create([
            'examination_id' => $exam->id,
            'user_id' => $student->id,
            'start_time' => now(),
            'total_score' => 85.00,
            'percentage' => 85.00,
            'is_passed' => true,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($student)->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Constitutional Law Mock Test');
        $response->assertSee('85%');

        // Cannot access judge or advocate dashboards
        $this->actingAs($student)->get(route('judge.dashboard'))->assertStatus(403);
        $this->actingAs($student)->get(route('advocate.dashboard'))->assertStatus(403);
    }

    public function test_advocate_can_access_advocate_dashboard(): void
    {
        $advocateRole = Role::where('name', 'advocate')->first();
        $advocate = User::factory()->create([
            'role' => 'advocate',
            'status' => 'active',
        ]);
        $advocate->roles()->attach($advocateRole);

        $response = $this->actingAs($advocate)->get(route('advocate.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Advocate Portal');
        $response->assertSee('Advocate Workspace');

        // Cannot access judge dashboard
        $this->actingAs($advocate)->get(route('judge.dashboard'))->assertStatus(403);
    }

    public function test_judge_can_access_judge_dashboard(): void
    {
        $judgeRole = Role::where('name', 'judge')->first();
        $judge = User::factory()->create([
            'role' => 'judge',
            'status' => 'active',
        ]);
        $judge->roles()->attach($judgeRole);

        $response = $this->actingAs($judge)->get(route('judge.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Judicial Bench');
        $response->assertSee('Judicial Officers Chambers');

        // Cannot access student dashboard
        $this->actingAs($judge)->get(route('student.dashboard'))->assertStatus(403);
    }

    public function test_super_admin_bypasses_and_can_access_all_dashboards(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('judge.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('advocate.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('student.dashboard'))->assertStatus(200);
        $this->actingAs($admin)->get(route('public.dashboard'))->assertStatus(200);
    }

    public function test_private_data_scoping_student_cannot_view_another_students_attempt(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $studentA = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $studentA->roles()->attach($studentRole);

        $studentB = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $studentB->roles()->attach($studentRole);

        $exam = Examination::create([
            'title' => 'Criminal Procedure Mock Test',
            'exam_code' => 'CRPC-201',
            'status' => 'published',
            'duration_minutes' => 45,
            'total_marks' => 50,
            'passing_marks' => 20,
        ]);

        $attemptA = ExaminationAttempt::create([
            'examination_id' => $exam->id,
            'user_id' => $studentA->id,
            'start_time' => now(),
            'total_score' => 42.00,
            'percentage' => 84.00,
            'is_passed' => true,
            'status' => 'completed',
        ]);

        // Student A can view their own result
        $this->actingAs($studentA)->get(route('exams.result', $attemptA->id))->assertStatus(200);

        // Student B CANNOT view Student A's result (403 Forbidden)
        $this->actingAs($studentB)->get(route('exams.result', $attemptA->id))->assertStatus(403);
    }

    public function test_dynamic_menu_and_module_permission_revocation(): void
    {
        $publicRole = Role::where('name', 'public')->first();
        $publicUser = User::factory()->create(['role' => 'public', 'status' => 'active']);
        $publicUser->roles()->attach($publicRole);

        // Initially has access to ask AI chatbot
        $this->actingAs($publicUser)->get(route('chatbot.ask'))->assertStatus(200);

        // Super Admin revokes AI chatbot access for Public role dynamically in RBAC matrix
        $aiModule = Module::where('slug', 'manage_ai_chatbot')->first();
        RoleModulePermission::where('role_id', $publicRole->id)
            ->where('module_id', $aiModule->id)
            ->update(['can_view' => false, 'can_create' => false]);

        // Server-side middleware CheckModuleAccess immediately blocks access with 403 Forbidden
        $this->actingAs($publicUser)->get(route('chatbot.ask'))->assertStatus(403);
    }

    public function test_subscription_based_access_status(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $student->roles()->attach($studentRole);

        // Initially without subscription
        $this->assertFalse($student->hasActiveSubscription());

        // Create package and active subscription
        $package = Package::create([
            'plan_code' => 'PLAN-TEST-1',
            'title' => 'Judicial Exam Master Pro',
            'current_price' => 1999.00,
            'duration_days' => 365,
            'status' => 'active',
        ]);

        Subscription::create([
            'user_id' => $student->id,
            'package_id' => $package->id,
            'start_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'active',
            'auto_renew' => true,
        ]);

        // Student now has active subscription
        $this->assertTrue($student->fresh()->hasActiveSubscription());
    }
}
