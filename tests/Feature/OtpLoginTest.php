<?php

namespace Tests\Feature;

use App\Mail\OtpLoginMail;
use App\Models\OtpVerification;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_otp_request_screen_can_be_rendered(): void
    {
        $response = $this->get('/auth/otp-login');
        $response->assertStatus(200);
        $response->assertSee('Instantly Login with OTP');
        $response->assertSee('Send Me');
    }

    public function test_user_can_request_otp(): void
    {
        Mail::fake();

        $user = User::create([
            'user_code' => '0509092420',
            'first_name' => 'OTP',
            'last_name' => 'User',
            'name' => 'OTP User',
            'email' => 'otp.user@lawuniverse.com',
            'password' => Hash::make('password123'),
            'dob' => '1995-05-15',
            'role' => 'public',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->post('/auth/otp-login/send', [
            'email' => 'otp.user@lawuniverse.com',
        ]);

        $response->assertRedirect(route('auth.otp.verify.form'));
        $this->assertDatabaseHas('otp_verifications', [
            'email' => 'otp.user@lawuniverse.com',
            'action_type' => 'instant_login',
        ]);

        Mail::assertSent(OtpLoginMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_user_can_verify_otp_and_login(): void
    {
        $user = User::create([
            'user_code' => '0509092421',
            'first_name' => 'Verified',
            'last_name' => 'User',
            'name' => 'Verified User',
            'email' => 'verified.otp@lawuniverse.com',
            'password' => Hash::make('password123'),
            'dob' => '1995-05-15',
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $otp = '654321';
        OtpVerification::create([
            'email' => $user->email,
            'otp_code' => $otp,
            'action_type' => 'instant_login',
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $response = $this->withSession(['otp_login_email' => $user->email])
            ->post('/auth/otp-login/verify', [
                'email' => $user->email,
                'otp' => $otp,
            ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_expired_or_invalid_otp_fails(): void
    {
        $user = User::create([
            'user_code' => '0509092422',
            'name' => 'Bad OTP User',
            'email' => 'bad.otp@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'public',
            'status' => 'active',
        ]);

        OtpVerification::create([
            'email' => $user->email,
            'otp_code' => '111111',
            'action_type' => 'instant_login',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->post('/auth/otp-login/verify', [
            'email' => $user->email,
            'otp' => '999999',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('otp');
    }
}

