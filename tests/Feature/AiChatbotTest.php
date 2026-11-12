<?php

namespace Tests\Feature;

use App\Models\AiVendor;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AiChatbotTest extends TestCase
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
            'name' => 'Advocate Researcher',
            'email' => 'advocate.ai@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->advocate->roles()->attach($advocateRole);

        AiVendor::create([
            'vendor_name' => 'OpenAI GPT-4o Enterprise',
            'api_base_url' => 'https://api.openai.com/v1',
            'api_key' => 'sk-test-fake-key-12345',
            'model_name' => 'gpt-4o',
            'is_active' => true,
        ]);
    }

    public function test_user_can_submit_legal_query_and_receive_citations(): void
    {
        $response = $this->actingAs($this->advocate)->postJson('/chatbot/ask', [
            'prompt' => 'Explain Article 21 and Right to Privacy precedents in India',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'conversation_id',
            'message_id',
            'reply',
            'vendor',
            'model',
            'tokens',
        ]);

        $reply = $response->json('reply');
        $this->assertStringContainsString('Puttaswamy', $reply);
        $this->assertStringContainsString('Article 21', $reply);

        // Assert database records
        $this->assertDatabaseHas('ai_conversations', ['user_id' => $this->advocate->id]);
        $this->assertDatabaseHas('ai_messages', ['sender' => 'assistant']);
        $this->assertDatabaseHas('ai_usage_logs', ['user_id' => $this->advocate->id]);
    }
}
