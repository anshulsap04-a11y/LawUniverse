<?php

namespace Tests\Feature;

use App\Mail\WelcomeLetterMail;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create account within a minute by filling simple form');
        $response->assertSee('Create Account with Google');
        $response->assertSee('Create Account with Facebook');
        $response->assertSee('Create My Account');
    }

    public function test_public_and_student_registration_is_auto_approved(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'first_name' => 'Alice',
            'last_name' => 'Student',
            'email' => 'alice.student@lawuniverse.com',
            'birthday' => '2002-04-15',
            'education' => 'Bachelor of Laws (LL.B.)',
            'role' => 'student',
            'residence_address' => 'Student Hostel 4',
            'mobile_number' => '9876543210',
            'password' => 'securePassword123',
            'country' => 'India',
            'receive_welcome_letter' => '1',
            'terms' => '1',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        $user = User::where('email', 'alice.student@lawuniverse.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertEquals('active', $user->status);
        $this->assertNotNull($user->approved_at);
        $this->assertNotNull($user->user_code);

        Mail::assertSent(WelcomeLetterMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_advocate_and_judge_registration_requires_approval(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Vikas',
            'last_name' => 'Counsel',
            'email' => 'vikas.advocate@lawuniverse.com',
            'birthday' => '1988-11-20',
            'education' => 'Master of Laws (LL.M.)',
            'role' => 'advocate',
            'residence_address' => 'High Court Bar Chamber 12',
            'mobile_number' => '9811223344',
            'password' => 'securePassword123',
            'country' => 'India',
            'terms' => '1',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('warning');

        $user = User::where('email', 'vikas.advocate@lawuniverse.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('advocate', $user->role);
        $this->assertEquals('pending', $user->status);
        $this->assertNull($user->approved_at);
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $response = $this->from('/register')->post('/register', [
            'first_name' => 'No',
            'last_name' => 'Terms',
            'email' => 'noterms@example.com',
            'birthday' => '1995-01-01',
            'role' => 'public',
            'password' => 'password123',
            // terms omitted
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('terms');
        $this->assertDatabaseMissing('users', ['email' => 'noterms@example.com']);
    }

    public function test_registration_requires_birthday(): void
    {
        $response = $this->from('/register')->post('/register', [
            'first_name' => 'No',
            'last_name' => 'Dob',
            'email' => 'nodob@example.com',
            'role' => 'public',
            'password' => 'password123',
            'terms' => '1',
            // birthday omitted
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('birthday');
    }
}

