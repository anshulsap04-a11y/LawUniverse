<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Coupon;
use App\Models\Role;
use App\Models\User;
use App\Services\FinanceService;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FinanceAndSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $advocate;
    protected Package $package;
    protected FinanceService $financeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        $this->financeService = app(FinanceService::class);

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
            'name' => 'Advocate Counsel',
            'email' => 'advocate@lawuniverse.com',
            'password' => Hash::make('password123'),
            'role' => 'advocate',
            'status' => 'active',
            'approved_at' => now(),
        ]);
        $this->advocate->roles()->attach($advocateRole);

        $this->package = Package::create([
            'plan_code' => 'ADV_PRO_2026',
            'title' => 'Annual Advocate Practice Suite',
            'current_price' => 1000.00,
            'duration_days' => 365,
            'status' => 'active',
        ]);
    }

    public function test_coupon_discount_calculation_and_subscription_creation(): void
    {
        $coupon = Coupon::create([
            'coupon_name' => 'Justice 20% Off',
            'coupon_code' => 'JUSTICE20',
            'discount_type' => 'percentage',
            'discount_value' => 20.0,
            'status' => 'active',
        ]);

        // 1. Validate Coupon
        $couponCheck = $this->financeService->applyCoupon('JUSTICE20', 1000.00, $this->advocate);
        $this->assertTrue($couponCheck['valid']);
        $this->assertEquals(200.00, $couponCheck['discount']);
        $this->assertEquals(800.00, $couponCheck['final_amount']);

        // 2. Subscribe User with Coupon
        $subscription = $this->financeService->subscribeUser($this->advocate, $this->package, $coupon);

        $this->assertNotNull($subscription);
        $this->assertEquals('active', $subscription->status);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->advocate->id,
            'package_id' => $this->package->id,
            'amount' => 800.00,
            'discount_amount' => 200.00,
            'status' => 'completed',
        ]);
    }
}
