<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('resource_categories')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('resource_type', 50)->default('general')->index(); // general, bare_act, legal_maxim, landmark_judgment, notes, previous_year_paper
            $table->foreignId('category_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->integer('year')->nullable()->index();
            $table->string('author')->nullable();
            $table->string('language', 50)->default('English');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_free')->default(true)->index();
            $table->boolean('is_copyright')->default(false)->index();
            $table->enum('status', ['published', 'draft', 'archived'])->default('published')->index();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('reference_link')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('resource_access_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->constrained('roles')->cascadeOnDelete();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_preview')->default(true);
            $table->boolean('can_download')->default(true);
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->timestamps();
        });

        Schema::create('bare_acts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->nullable()->constrained('resources')->cascadeOnDelete();
            $table->string('act_name');
            $table->string('act_number')->nullable();
            $table->integer('year')->nullable()->index();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->string('jurisdiction')->default('Central / National (India)');
            $table->string('language')->default('English');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('amendment_status')->default('Up to date');
            $table->date('publication_date')->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_copyright')->default(false);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('legal_maxims', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Maxim Title
            $table->string('latin_maxim')->nullable()->index();
            $table->text('english_meaning')->nullable();
            $table->text('hindi_meaning')->nullable();
            $table->text('description')->nullable();
            $table->longText('legal_explanation')->nullable();
            $table->text('example')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('landmark_judgments', function (Blueprint $table) {
            $table->id();
            $table->string('case_name');
            $table->string('case_number')->nullable();
            $table->string('court')->default('Supreme Court of India')->index();
            $table->string('bench')->nullable();
            $table->date('judgment_date')->nullable()->index();
            $table->string('citation')->nullable()->index();
            $table->text('legal_principle')->nullable();
            $table->longText('case_summary')->nullable();
            $table->longText('full_judgment')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('resource_categories')->nullOnDelete();
            $table->integer('year')->nullable()->index();
            $table->string('language')->default('English');
            $table->string('document_path')->nullable();
            $table->boolean('is_free')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('category')->nullable();
            $table->string('tags')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_private')->default(true);
            $table->boolean('is_favorite')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('landmark_judgments');
        Schema::dropIfExists('legal_maxims');
        Schema::dropIfExists('bare_acts');
        Schema::dropIfExists('resource_access_rules');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('resource_categories');
    }
};
