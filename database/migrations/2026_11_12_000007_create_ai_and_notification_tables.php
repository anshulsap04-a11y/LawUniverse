<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_name'); // OpenAI, Anthropic, Google Gemini, Custom Legal LLM
            $table->string('api_base_url')->default('https://api.openai.com/v1');
            $table->text('api_key')->nullable(); // encrypted
            $table->string('model_name')->default('gpt-4o');
            $table->string('api_version')->nullable();
            $table->json('configuration')->nullable();
            $table->string('email_address')->nullable();
            $table->date('validity_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('allowed_roles')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('ai_vendors')->nullOnDelete();
            $table->string('title');
            $table->string('context_type')->default('general');
            $table->string('model_used')->nullable();
            $table->decimal('charge', 8, 4)->default(0.0000);
            $table->dateTime('last_activity_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->enum('sender', ['user', 'assistant'])->default('user');
            $table->longText('message');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable(); // image, pdf
            $table->json('legal_citations')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 8, 4)->default(0.0000);
            $table->timestamps();
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('ai_vendors')->nullOnDelete();
            $table->string('request_type')->default('chat');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('estimated_cost', 8, 4)->default(0.0000);
            $table->string('status')->default('success');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 50)->unique();
            $table->string('subject');
            $table->longText('message');
            $table->enum('recipient_type', ['all', 'role', 'individual'])->default('all');
            $table->json('target_roles')->nullable();
            $table->json('target_users')->nullable();
            $table->dateTime('post_date')->nullable();
            $table->enum('status', ['published', 'draft', 'scheduled'])->default('published');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->string('type', 30)->default('string'); // string, boolean, json, integer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notification_recipients');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_vendors');
    }
};

