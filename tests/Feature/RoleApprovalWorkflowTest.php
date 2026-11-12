<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $pendingJudge;
    protected User $pendingAdvocate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();

        $this->superAdmin = User::create([
            'user_code' => '012205243',
            'name' => 'Super Administrator',
            'email' => 'admin@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->superAdmin->roles()->attach($superAdminRole);

        $this->pendingJudge = User::create([
            'user_code' => '0516092411',
            'first_name' => 'Judicial',
            'last_name' => 'Applicant',
            'name' => 'Judicial Applicant',
            'email' => 'judge.app@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'judge',
            'status' => 'pending',
            'approved_at' => null,
        ]);

        $this->pendingAdvocate = User::create([
            'user_code' => '0509092411',
            'first_name' => 'Advocate',
            'last_name' => 'Applicant',
            'name' => 'Advocate Applicant',
            'email' => 'advocate.app@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'advocate',
            'status' => 'pending',
            'approved_at' => null,
        ]);
    }

    public function test_super_admin_can_access_dashboard_and_see_pending_approvals(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Super Admin Control Center');
        $response->assertSee('Role Approval Workflow');
        $response->assertSee('judge.app@lawuniverse.com');
        $response->assertSee('advocate.app@lawuniverse.com');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $student = User::create([
            'user_code' => '0509092440',
            'name' => 'Regular Student',
            'email' => 'student.reg@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_approve_pending_user(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post("/admin/users/{$this->pendingAdvocate->id}/approve");

        $response->assertSessionHas('success');

        $this->pendingAdvocate->refresh();
        $this->assertEquals('active', $this->pendingAdvocate->status);
        $this->assertNotNull($this->pendingAdvocate->approved_at);
        $this->assertEquals($this->superAdmin->id, $this->pendingAdvocate->approved_by);

        // Now the advocate can log in!
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'advocate.app@lawuniverse.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->pendingAdvocate);
        $loginResponse->assertRedirect(route('advocate.dashboard'));
    }

    public function test_super_admin_can_reject_pending_user(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post("/admin/users/{$this->pendingJudge->id}/reject");

        $response->assertSessionHas('warning');

        $this->pendingJudge->refresh();
        $this->assertEquals('rejected', $this->pendingJudge->status);
    }

    public function test_super_admin_can_toggle_user_status(): void
    {
        $user = User::create([
            'user_code' => '0509092445',
            'name' => 'Active Citizen',
            'email' => 'citizen@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
            'status' => 'active',
        ]);

        // Toggle to deactivated
        $response = $this->actingAs($this->superAdmin)
            ->post("/admin/users/{$user->id}/toggle-status");

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('deactivated', $user->status);

        // Toggle back to active
        $this->actingAs($this->superAdmin)
            ->post("/admin/users/{$user->id}/toggle-status");

        $user->refresh();
        $this->assertEquals('active', $user->status);
    }

    public function test_multi_trash_can_bulk_delete_users(): void
    {
        $user1 = User::create([
            'user_code' => '0509092446',
            'name' => 'User One',
            'email' => 'user1@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
            'status' => 'active',
        ]);

        $user2 = User::create([
            'user_code' => '0509092447',
            'name' => 'User Two',
            'email' => 'user2@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/users/bulk-trash', [
                'selected_ids' => [$user1->id, $user2->id],
            ]);

        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]);
    }
}

