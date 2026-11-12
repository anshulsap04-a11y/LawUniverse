<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $studentRole = Role::where('name', 'student')->first();

        $this->superAdmin = User::create([
            'user_code' => 'ADMIN001',
            'name' => 'Super Administrator',
            'email' => 'admin@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->superAdmin->roles()->attach($superAdminRole);

        $this->student = User::create([
            'user_code' => 'STU001',
            'name' => 'Law Student',
            'email' => 'student.portal@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->student->roles()->attach($studentRole);
    }

    public function test_super_admin_can_impersonate_user_and_exit_cleanly(): void
    {
        // 1. Super Admin impersonates Student
        $response = $this->actingAs($this->superAdmin)->post("/admin/impersonate/{$this->student->id}");
        $response->assertRedirect('/student/dashboard');

        // Current authenticated user is now student
        $this->assertEquals($this->student->id, auth()->id());
        $this->assertEquals($this->superAdmin->id, session('impersonating_admin_id'));

        // 2. Impersonated user can browse and exit impersonation
        $stopResponse = $this->post('/impersonation/stop');
        $stopResponse->assertRedirect('/admin/dashboard');

        // User is restored back to Super Admin
        $this->assertEquals($this->superAdmin->id, auth()->id());
        $this->assertNull(session('impersonating_admin_id'));
    }
}

