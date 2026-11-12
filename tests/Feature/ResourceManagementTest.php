<?php

namespace Tests\Feature;

use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\BareAct;
use App\Models\LegalMaxim;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $student;
    protected ResourceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        Storage::fake('public');

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
            'name' => 'Student Reader',
            'email' => 'student@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->student->roles()->attach($studentRole);

        $this->category = ResourceCategory::create([
            'name' => 'Constitutional Law',
            'slug' => 'constitutional-law',
        ]);
    }

    public function test_super_admin_can_upload_and_clone_resource(): void
    {
        $file = UploadedFile::fake()->create('constitution-digest.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->superAdmin)->post('/admin/resources', [
            'title' => 'Constitutional Law Comprehensive Digest 2026',
            'category_id' => $this->category->id,
            'resource_type' => 'general',
            'description' => 'Detailed notes on fundamental rights and judicial review.',
            'price' => 0.00,
            'is_free' => 1,
            'status' => 'published',
            'file' => $file,
        ]);

        $response->assertRedirect('/admin/resources');
        $this->assertDatabaseHas('resources', [
            'title' => 'Constitutional Law Comprehensive Digest 2026',
            'is_free' => true,
        ]);

        $resource = Resource::where('title', 'Constitutional Law Comprehensive Digest 2026')->first();
        $this->assertNotNull($resource->file_path);

        // Test Clone
        $cloneResponse = $this->actingAs($this->superAdmin)->post("/admin/resources/{$resource->id}/clone");
        $cloneResponse->assertRedirect('/admin/resources');
        $this->assertDatabaseHas('resources', [
            'title' => 'Constitutional Law Comprehensive Digest 2026 (Copy)',
            'status' => 'draft',
        ]);
    }

    public function test_super_admin_can_catalogue_bare_act_and_maxim(): void
    {
        // 1. Bare Act
        $actResponse = $this->actingAs($this->superAdmin)->post('/admin/resources/bare-acts', [
            'act_name' => 'Bharatiya Sakshya Adhiniyam, 2023',
            'act_number' => 'Act 47',
            'year' => 2023,
            'description' => 'New evidence statute replacing Indian Evidence Act 1872.',
        ]);
        $actResponse->assertSessionHas('success');
        $this->assertDatabaseHas('bare_acts', ['act_name' => 'Bharatiya Sakshya Adhiniyam, 2023']);

        // 2. Legal Maxim
        $maximResponse = $this->actingAs($this->superAdmin)->post('/admin/resources/legal-maxims', [
            'latin_maxim' => 'Nemo debet esse judex in propria causa',
            'english_meaning' => 'No one should be a judge in their own cause',
            'origin' => 'Natural Justice Doctrine',
            'explanation' => 'Rule against bias in administrative and judicial decision-making.',
        ]);
        $maximResponse->assertSessionHas('success');
        $this->assertDatabaseHas('legal_maxims', ['latin_maxim' => 'Nemo debet esse judex in propria causa']);
    }
}
