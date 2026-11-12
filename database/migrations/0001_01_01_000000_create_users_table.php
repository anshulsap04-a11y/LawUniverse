<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_code', 30)->nullable()->unique();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->date('dob')->nullable();
            $table->string('education', 100)->nullable();
            $table->string('role', 50)->default('public')->index();
            $table->text('address')->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->text('about')->nullable();
            $table->string('country', 100)->default('India');
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active', 'pending', 'deactivated', 'rejected'])->default('active')->index();
            $table->boolean('receive_welcome_letter')->default(true);
            $table->boolean('terms_accepted')->default(true);
            $table->boolean('enable_2fa')->default(false);
            $table->boolean('enable_instant_otp')->default(true);
            $table->string('google_id')->nullable()->index();
            $table->string('facebook_id')->nullable()->index();
            $table->string('oauth_provider', 30)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
