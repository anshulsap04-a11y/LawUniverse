<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCustomMail;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee("We'll forward reset instruction to your email", false);
        $response->assertSee('Date of Birth');
        $response->assertSee('Send Me');
    }

    public function test_reset_link_sent_when_email_and_dob_match(): void
    {
        Mail::fake();

        $user = User::create([
            'user_code' => '0509092430',
            'first_name' => 'Reset',
            'last_name' => 'Candidate',
            'name' => 'Reset Candidate',
            'email' => 'reset.candidate@lawuniverse.com',
            'password' => Hash::make('oldPassword123'),
            'dob' => '1993-08-25',
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'reset.candidate@lawuniverse.com',
            'dob' => '1993-08-25',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

        Mail::assertSent(PasswordResetCustomMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_link_rejected_when_dob_does_not_match(): void
    {
        $user = User::create([
            'user_code' => '0509092431',
            'first_name' => 'Mismatch',
            'last_name' => 'Dob',
            'name' => 'Mismatch Dob',
            'email' => 'mismatch@lawuniverse.com',
            'password' => Hash::make('password123'),
            'dob' => '1990-01-01',
            'role' => 'public',
            'status' => 'active',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'mismatch@lawuniverse.com',
            'dob' => '2000-12-31', // Wrong DOB!
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::create([
            'user_code' => '0509092432',
            'first_name' => 'Final',
            'last_name' => 'Reset',
            'name' => 'Final Reset',
            'email' => 'final.reset@lawuniverse.com',
            'password' => Hash::make('oldSecret123'),
            'dob' => '1991-02-14',
            'role' => 'judge',
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $plainToken = 'sample-plain-token-123456';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        // Show reset password view
        $viewResponse = $this->get('/reset-password/' . $plainToken . '?email=' . urlencode($user->email));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Set New Password');

        // Submit new password
        $response = $this->post('/reset-password', [
            'token' => $plainToken,
            'email' => $user->email,
            'password' => 'newSecretPassword888',
            'password_confirmation' => 'newSecretPassword888',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Verify password updated in DB
        $user->refresh();
        $this->assertTrue(Hash::check('newSecretPassword888', $user->password));

        // Token consumed and deleted
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }
}
