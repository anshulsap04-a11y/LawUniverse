<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('examination_categories')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('examination_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label_type', 50)->default('First Label'); // First Label or Second Label
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_code', 50)->unique();
            $table->text('question_title');
            $table->longText('question_description')->nullable();
            $table->enum('question_type', ['mcq', 'multiple_select', 'true_false', 'short_answer', 'descriptive'])->default('mcq');
            $table->json('options')->nullable(); // array of options [{id: 'A', text: '...'}, ...]
            $table->text('correct_answer'); // e.g. "A" or option key
            $table->longText('explanation')->nullable();
            $table->decimal('marks', 8, 2)->default(1.00);
            $table->decimal('negative_marks', 8, 2)->default(0.00);
            $table->foreignId('category_id')->nullable()->constrained('examination_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('examination_categories')->nullOnDelete();
            $table->foreignId('first_label_id')->nullable()->constrained('examination_labels')->nullOnDelete();
            $table->foreignId('second_label_id')->nullable()->constrained('examination_labels')->nullOnDelete();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('year')->nullable();
            $table->boolean('is_interactive')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('exam_code', 50)->unique();
            $table->longText('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('examination_categories')->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('examination_categories')->nullOnDelete();
            $table->foreignId('first_label_id')->nullable()->constrained('examination_labels')->nullOnDelete();
            $table->foreignId('second_label_id')->nullable()->constrained('examination_labels')->nullOnDelete();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->decimal('total_marks', 8, 2)->default(100.00);
            $table->decimal('passing_marks', 8, 2)->default(40.00);
            $table->decimal('cutoff_percentage', 5, 2)->default(40.00);
            $table->decimal('negative_marking_ratio', 4, 2)->default(0.00);
            $table->boolean('flag_negative_marking')->default(false);
            $table->longText('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published')->index();
            $table->integer('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_result_immediately')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('examination_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained('examinations')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->decimal('marks', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['examination_id', 'question_id']);
        });

        Schema::create('previous_year_papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('exam_name');
            $table->integer('year')->index();
            $table->foreignId('category_id')->nullable()->constrained('examination_categories')->nullOnDelete();
            $table->string('subject')->nullable();
            $table->string('file_path')->nullable();
            $table->string('answer_key_path')->nullable();
            $table->longText('explanation')->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_copyright')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('examination_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('examination_id')->constrained('examinations')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->decimal('total_score', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->integer('correct_count')->default(0);
            $table->integer('incorrect_count')->default(0);
            $table->integer('unanswered_count')->default(0);
            $table->boolean('is_passed')->default(false);
            $table->enum('status', ['in_progress', 'completed', 'timed_out'])->default('in_progress');
            $table->timestamps();
        });

        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('examination_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 8, 2)->default(0.00);
            $table->boolean('is_marked_for_review')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('examination_attempts');
        Schema::dropIfExists('previous_year_papers');
        Schema::dropIfExists('examination_questions');
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('examination_labels');
        Schema::dropIfExists('examination_categories');
    }
};
