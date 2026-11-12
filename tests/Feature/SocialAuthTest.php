<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_google_redirect_initiates_oauth(): void
    {
        $response = $this->get('/auth/google');
        // Socialite redirects to Google accounts URL
        $this->assertTrue($response->isRedirection());
    }

    public function test_facebook_redirect_initiates_oauth(): void
    {
        $response = $this->get('/auth/facebook');
        $this->assertTrue($response->isRedirection());
    }

    public function test_existing_social_user_is_logged_in_directly(): void
    {
        $user = User::create([
            'user_code' => '0509092460',
            'name' => 'Existing Google User',
            'email' => 'google.user@example.com',
            'google_id' => '1234567890',
            'oauth_provider' => 'google',
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('1234567890');
        $abstractUser->shouldReceive('getEmail')->andReturn('google.user@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Existing Google User');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('advocate.dashboard'));
    }

    public function test_new_social_user_is_redirected_to_role_selection(): void
    {
        $abstractUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('987654321');
        $abstractUser->shouldReceive('getEmail')->andReturn('new.google.user@example.com');
        $abstractUser->shouldReceive('getName')->andReturn('Sohan Chandel');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('auth.social.role'));
        $response->assertSessionHas('oauth_pending_user');

        // Role selection screen renders
        $roleScreenResponse = $this->get('/signup');
        $roleScreenResponse->assertStatus(200);
        $roleScreenResponse->assertSee('Select User Role');
        $roleScreenResponse->assertSee('Public');
        $roleScreenResponse->assertSee('Student');
        $roleScreenResponse->assertSee('Advocate');
        $roleScreenResponse->assertSee('Judge');
    }

    public function test_new_social_user_submits_role_and_completes_registration(): void
    {
        $sessionData = [
            'provider' => 'google',
            'provider_id' => '987654321',
            'email' => 'sohan.advocate@example.com',
            'name' => 'Sohan Chandel',
            'first_name' => 'Sohan',
            'last_name' => 'Chandel',
            'avatar' => null,
        ];

        // Choosing Advocate role -> should be pending approval
        $response = $this->withSession(['oauth_pending_user' => $sessionData])
            ->post('/signup/select-role', [
                'role' => 'advocate',
            ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning');

        $user = User::where('email', 'sohan.advocate@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('advocate', $user->role);
        $this->assertEquals('pending', $user->status);
        $this->assertEquals('google', $user->oauth_provider);
        $this->assertEquals('987654321', $user->google_id);
    }
}

