<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('plan_code', 50)->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('tagline')->nullable();
            $table->decimal('pre_discount_price', 10, 2)->default(0.00);
            $table->decimal('current_price', 10, 2)->default(0.00);
            $table->integer('duration_days')->default(30); // 30 = 1 month, 365 = 1 year
            $table->longText('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('module_scope')->nullable(); // e.g. manage_resources, manage_examinations
            $table->enum('feature_type', ['boolean', 'usage_limit', 'download_limit', 'time_access'])->default('boolean');
            $table->string('default_value')->nullable();
            $table->timestamps();
        });

        Schema::create('package_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->string('value')->default('1');
            $table->timestamps();

            $table->unique(['package_id', 'feature_id']);
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('coupon_name');
            $table->string('coupon_code', 50)->unique();
            $table->text('description')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2); // e.g. 20.00 for 20%
            $table->dateTime('start_date')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('per_user_limit')->default(1);
            $table->foreignId('applicable_package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->dateTime('start_date');
            $table->dateTime('expiry_date');
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('active')->index();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_gateway')->default('Razorpay');
            $table->string('payment_method')->default('Card');
            $table->string('invoice_number', 50)->nullable()->unique();
            $table->enum('status', ['completed', 'pending', 'failed', 'refunded'])->default('completed');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('features');
        Schema::dropIfExists('packages');
    }
};
