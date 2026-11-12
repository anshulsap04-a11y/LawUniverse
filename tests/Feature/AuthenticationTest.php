<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login in to your LawUniverse account');
        $response->assertSee('Continue with Google');
        $response->assertSee('Continue with Facebook');
        $response->assertSee('Forgot Password?');
        $response->assertSee('Log in without your password instantly');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'user_code' => '0509092411',
            'first_name' => 'John',
            'last_name' => 'Advocate',
            'name' => 'John Advocate',
            'email' => 'john.advocate@lawuniverse.com',
            'password' => Hash::make('password123'),
            'dob' => '1990-01-01',
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'john.advocate@lawuniverse.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('advocate.dashboard'));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::create([
            'user_code' => '0509092411',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
            'status' => 'active',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_pending_advocate_or_judge_cannot_login(): void
    {
        $pendingAdvocate = User::create([
            'user_code' => '0509092412',
            'first_name' => 'Pending',
            'last_name' => 'Advocate',
            'name' => 'Pending Advocate',
            'email' => 'pending.advocate@lawuniverse.com',
            'password' => Hash::make('password123'),
            'dob' => '1992-05-10',
            'role' => 'advocate',
            'status' => 'pending',
            'approved_at' => null,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'pending.advocate@lawuniverse.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('warning');
    }

    public function test_deactivated_user_cannot_login(): void
    {
        $deactivated = User::create([
            'user_code' => '0509092413',
            'first_name' => 'Deactivated',
            'last_name' => 'User',
            'name' => 'Deactivated User',
            'email' => 'deactivated@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'deactivated',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'deactivated@lawuniverse.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }

    public function test_role_based_redirects_after_login(): void
    {
        $rolesAndRoutes = [
            'super_admin' => route('admin.dashboard'),
            'admin' => route('admin.dashboard'),
            'judge' => route('judge.dashboard'),
            'advocate' => route('advocate.dashboard'),
            'student' => route('student.dashboard'),
            'public' => route('public.dashboard'),
        ];

        foreach ($rolesAndRoutes as $roleName => $expectedRoute) {
            $user = User::create([
                'user_code' => sprintf('%010d', rand(1000, 99999)),
                'first_name' => ucfirst($roleName),
                'last_name' => 'Tester',
                'name' => ucfirst($roleName) . ' Tester',
                'email' => "{$roleName}.tester@lawuniverse.com",
                'password' => Hash::make('secret123'),
                'dob' => '1990-01-01',
                'role' => $roleName,
                'status' => 'active',
                'approved_at' => now(),
            ]);

            $response = $this->post('/login', [
                'email' => "{$roleName}.tester@lawuniverse.com",
                'password' => 'secret123',
            ]);

            $this->assertAuthenticatedAs($user);
            $response->assertRedirect($expectedRoute);

            // Log out for next iteration
            $this->post('/logout');
            $this->assertGuest();
        }
    }

    public function test_users_can_logout(): void
    {
        $user = User::create([
            'user_code' => '0509092415',
            'name' => 'Logout User',
            'email' => 'logout@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}

