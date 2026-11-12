<?php

namespace Tests\Feature;

use App\Models\Examination;
use App\Models\ExaminationCategory;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExaminationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $student;
    protected Examination $examination;
    protected Question $q1;
    protected Question $q2;

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
            'name' => 'Bar Aspirant',
            'email' => 'aspirant@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->student->roles()->attach($studentRole);

        $cat = ExaminationCategory::create(['name' => 'All India Bar Examination', 'slug' => 'aibe']);

        $this->q1 = Question::create([
            'question_code' => 'Q_CONST_001',
            'question_title' => 'Under which Article of the Constitution of India is the Right to Equality guaranteed?',
            'question_type' => 'mcq',
            'options' => [
                ['id' => 'A', 'text' => 'Article 14'],
                ['id' => 'B', 'text' => 'Article 19'],
                ['id' => 'C', 'text' => 'Article 21'],
                ['id' => 'D', 'text' => 'Article 32'],
            ],
            'correct_answer' => 'A',
            'marks' => 2.0,
            'category_id' => $cat->id,
            'difficulty' => 'easy',
        ]);

        $this->q2 = Question::create([
            'question_code' => 'Q_BNS_001',
            'question_title' => 'Which section of the Bharatiya Nyaya Sanhita, 2023 defines Murder?',
            'question_type' => 'mcq',
            'options' => [
                ['id' => 'A', 'text' => 'Section 101'],
                ['id' => 'B', 'text' => 'Section 103'],
                ['id' => 'C', 'text' => 'Section 302'],
                ['id' => 'D', 'text' => 'Section 107'],
            ],
            'correct_answer' => 'B',
            'marks' => 2.0,
            'category_id' => $cat->id,
            'difficulty' => 'medium',
        ]);

        $this->examination = Examination::create([
            'exam_code' => 'EX_AIBE_001',
            'category_id' => $cat->id,
            'title' => 'AIBE Qualifying Practice Mock',
            'duration_minutes' => 30,
            'total_marks' => 4.0,
            'cutoff_percentage' => 50,
            'status' => 'published',
        ]);

        $this->examination->questions()->attach([
            $this->q1->id => ['order' => 1],
            $this->q2->id => ['order' => 2],
        ]);
    }

    public function test_student_can_attempt_and_submit_examination_with_instant_scoring(): void
    {
        // 1. Start Attempt
        $startResponse = $this->actingAs($this->student)->post("/exams/start/{$this->examination->id}");
        $startResponse->assertRedirect();

        $attempt = $this->student->examinationAttempts()->where('examination_id', $this->examination->id)->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('in_progress', $attempt->status);

        // 2. Answer Question 1 correctly (A)
        $ans1Response = $this->actingAs($this->student)->postJson("/exams/attempt/{$attempt->id}/answer", [
            'question_id' => $this->q1->id,
            'selected_option' => 'A',
        ]);
        $ans1Response->assertStatus(200);
        $ans1Response->assertJson(['status' => 'success']);

        // 3. Answer Question 2 correctly (B)
        $ans2Response = $this->actingAs($this->student)->postJson("/exams/attempt/{$attempt->id}/answer", [
            'question_id' => $this->q2->id,
            'selected_option' => 'B',
        ]);
        $ans2Response->assertStatus(200);

        // 4. Submit Examination
        $submitResponse = $this->actingAs($this->student)->post("/exams/attempt/{$attempt->id}/submit");
        $submitResponse->assertRedirect("/exams/result/{$attempt->id}");

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertEquals(2, $attempt->correct_count);
        $this->assertEquals(0, $attempt->incorrect_count);
        $this->assertEquals(4.0, $attempt->total_score);
        $this->assertEquals(100.0, $attempt->percentage);
        $this->assertTrue((bool)$attempt->is_passed);

        // 5. Result View asserts pass message
        $resultView = $this->actingAs($this->student)->get("/exams/result/{$attempt->id}");
        $resultView->assertStatus(200);
        $resultView->assertSee('Congratulations! You Passed');
    }
}
