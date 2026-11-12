<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Coupon;
use App\Models\Examination;
use App\Models\ExaminationCategory;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPdfModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $advocate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $advocateRole = Role::where('name', 'advocate')->first();

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

        $this->advocate = User::create([
            'user_code' => 'ADV001',
            'name' => 'Advocate User',
            'email' => 'advocate@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->advocate->roles()->attach($advocateRole);
    }

    public function test_module3_examination_question_and_multi_trash(): void
    {
        $cat = ExaminationCategory::create(['name' => 'Bar Council Exam', 'slug' => 'bce']);

        // Create questions
        $q1 = Question::create([
            'question_code' => 'Q-001',
            'question_title' => 'Sample Legal Question 1',
            'question_type' => 'mcq',
            'options' => [['id' => 'A', 'text' => 'Opt A'], ['id' => 'B', 'text' => 'Opt B']],
            'correct_answer' => 'A',
            'category_id' => $cat->id,
            'marks' => 1.0,
            'difficulty' => 'easy',
        ]);

        $q2 = Question::create([
            'question_code' => 'Q-002',
            'question_title' => 'Sample Legal Question 2',
            'question_type' => 'mcq',
            'options' => [['id' => 'A', 'text' => 'Opt A'], ['id' => 'B', 'text' => 'Opt B']],
            'correct_answer' => 'B',
            'category_id' => $cat->id,
            'marks' => 1.0,
            'difficulty' => 'medium',
        ]);

        // Super Admin bulk trashes questions (Page 19 Item 6)
        $bulkResponse = $this->actingAs($this->superAdmin)->post('/admin/examinations/questions/bulk-trash', [
            'ids' => [$q1->id, $q2->id],
        ]);
        $bulkResponse->assertSessionHas('success');
        $this->assertSoftDeleted('questions', ['id' => $q1->id]);
        $this->assertSoftDeleted('questions', ['id' => $q2->id]);
    }

    public function test_module4_blogging_compose_and_multi_trash(): void
    {
        $cat = BlogCategory::create(['name' => 'Judicial Insights', 'slug' => 'judicial-insights']);

        $blog1 = Blog::create([
            'category_id' => $cat->id,
            'author_id' => $this->superAdmin->id,
            'title' => 'Article One Title',
            'slug' => 'article-one-title',
            'content' => 'Full article content for testing...',
            'status' => 'published',
        ]);

        $blog2 = Blog::create([
            'category_id' => $cat->id,
            'author_id' => $this->superAdmin->id,
            'title' => 'Article Two Title',
            'slug' => 'article-two-title',
            'content' => 'Full article content for testing...',
            'status' => 'published',
        ]);

        // Bulk trash blogs (Page 28 Item 4)
        $trashResponse = $this->actingAs($this->superAdmin)->post('/admin/blogs/bulk-trash', [
            'ids' => [$blog1->id, $blog2->id],
        ]);
        $trashResponse->assertSessionHas('success');
        $this->assertSoftDeleted('blogs', ['id' => $blog1->id]);
        $this->assertSoftDeleted('blogs', ['id' => $blog2->id]);
    }

    public function test_module5_finance_coupon_toggle_and_package_trash(): void
    {
        $pkg = Package::create([
            'plan_code' => 'PKG-TEST',
            'title' => 'Test Legal Package',
            'current_price' => 500.00,
            'duration_days' => 30,
            'status' => 'active',
        ]);

        $coupon = Coupon::create([
            'coupon_name' => 'Test Discount Voucher',
            'coupon_code' => 'DISC50',
            'discount_type' => 'percentage',
            'discount_value' => 50.0,
            'status' => 'active',
        ]);

        // Toggle coupon status (Page 36 Item 8)
        $toggleResponse = $this->actingAs($this->superAdmin)->post("/admin/finance/coupons/{$coupon->id}/toggle");
        $toggleResponse->assertSessionHas('success');
        $coupon->refresh();
        $this->assertEquals('inactive', $coupon->status);

        // Bulk delete packages (Page 31 Item 3)
        $pkgTrashResponse = $this->actingAs($this->superAdmin)->post('/admin/finance/packages/bulk-trash', [
            'ids' => [$pkg->id],
        ]);
        $pkgTrashResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('packages', ['id' => $pkg->id]);
    }

    public function test_module7_notification_broadcast_and_multi_remove(): void
    {
        $notif1 = Notification::create([
            'subject_code' => 'N-001',
            'subject' => 'Broadcast Notice 1',
            'message' => 'First message alert',
            'recipient_type' => 'all',
            'status' => 'published',
        ]);

        $notif2 = Notification::create([
            'subject_code' => 'N-002',
            'subject' => 'Broadcast Notice 2',
            'message' => 'Second message alert',
            'recipient_type' => 'all',
            'status' => 'published',
        ]);

        // Bulk remove notifications (Page 41 Item 4)
        $delResponse = $this->actingAs($this->superAdmin)->post('/admin/notifications/bulk-trash', [
            'ids' => [$notif1->id, $notif2->id],
        ]);
        $delResponse->assertSessionHas('success');
        $this->assertSoftDeleted('notifications', ['id' => $notif1->id]);
        $this->assertSoftDeleted('notifications', ['id' => $notif2->id]);
    }
}
