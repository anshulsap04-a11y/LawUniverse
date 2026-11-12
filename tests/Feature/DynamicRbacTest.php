<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DynamicRbacTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $student;
    protected Role $studentRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $this->studentRole = Role::where('name', 'student')->first();

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

        $this->student = User::create([
            'user_code' => '0509092450',
            'name' => 'Student Tester',
            'email' => 'student.tester@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->student->roles()->attach($this->studentRole);
    }

    public function test_super_admin_can_view_rbac_matrix(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/rbac');
        $response->assertStatus(200);
        $response->assertSee('Dynamic RBAC & Module Permission Matrix', false);
        $response->assertSee('Manage User Account');
        $response->assertSee('Manage Resource');
        $response->assertSee('Manage Examination');
        $response->assertSee('Manage AI Chatbot');
    }

    public function test_super_admin_can_create_custom_role(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/admin/rbac/roles', [
            'display_name' => 'Legal Researcher',
            'description' => 'Research fellow analyzing judgments and bare acts',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', ['name' => 'legal_researcher']);
    }

    public function test_dynamic_permission_grant_and_enforcement(): void
    {
        $chatbotModule = Module::where('slug', 'manage_ai_chatbot')->first();

        // 1. Ensure student does not have chatbot module permission initially
        RoleModulePermission::where('role_id', $this->studentRole->id)
            ->where('module_id', $chatbotModule->id)
            ->delete();

        // Acting as student -> should get 403 Forbidden
        $response = $this->actingAs($this->student)->get('/chatbot/ask');
        $response->assertStatus(403);

        // 2. Super admin dynamically grants 'can_view' permission to Student role for AI Chatbot
        $updateResponse = $this->actingAs($this->superAdmin)->post('/admin/rbac/matrix', [
            'role_id' => $this->studentRole->id,
            'permissions' => [
                $chatbotModule->id => [
                    0 => ['can_view' => '1'],
                ],
            ],
        ]);
        $updateResponse->assertSessionHas('success');

        // 3. Student can now immediately access /chatbot/ask!
        $allowedResponse = $this->actingAs($this->student)->get('/chatbot/ask');
        $allowedResponse->assertStatus(200);
        $allowedResponse->assertSee('Ask anything to AI Chatbot');
    }

    public function test_super_admin_bypasses_all_module_permission_checks(): void
    {
        // Super admin can access chatbot even if no explicit role_module_permissions row exists
        $response = $this->actingAs($this->superAdmin)->get('/chatbot/ask');
        $response->assertStatus(200);
        $response->assertSee('Ask anything to AI Chatbot');
    }
}
