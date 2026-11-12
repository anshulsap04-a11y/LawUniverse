<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FinanceService
{
    /**
     * Validate and calculate discount for a coupon.
     */
    public function applyCoupon(string $code, float $amount, User $user): array
    {
        $coupon = Coupon::where('coupon_code', strtoupper(trim($code)))
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid or inactive coupon code.'];
        }

        $now = Carbon::now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return ['valid' => false, 'message' => 'Coupon is not yet active.'];
        }

        if ($coupon->expiration_date && $now->gt($coupon->expiration_date)) {
            return ['valid' => false, 'message' => 'Coupon has expired.'];
        }

        // Calculate discount
        $discount = 0.0;
        if ($coupon->discount_type === 'percentage') {
            $discount = round(($amount * $coupon->discount_value) / 100, 2);
        } else {
            $discount = min($amount, (float)$coupon->discount_value);
        }

        $finalAmount = max(0.00, $amount - $discount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'final_amount' => $finalAmount,
        ];
    }

    /**
     * Subscribe user to a package.
     */
    public function subscribeUser(User $user, Package $package, ?Coupon $coupon = null, string $paymentMethod = 'Card'): Subscription
    {
        $amount = (float)$package->current_price;
        $discount = 0.0;

        if ($coupon) {
            $couponResult = $this->applyCoupon($coupon->coupon_code, $amount, $user);
            if ($couponResult['valid']) {
                $discount = $couponResult['discount'];
                $amount = $couponResult['final_amount'];
            }
        }

        // Deactivate previous active subscriptions
        $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);

        $startsAt = Carbon::now();
        $endsAt = $startsAt->copy()->addDays($package->duration_days);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'active',
            'start_date' => $startsAt,
            'expiry_date' => $endsAt,
            'auto_renew' => false,
        ]);

        // Create Transaction record
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => 'TXN-' . strtoupper(Str::random(10)),
            'package_id' => $package->id,
            'amount' => $amount,
            'tax' => 0.00,
            'discount_amount' => $discount,
            'total_amount' => $amount,
            'payment_gateway' => 'Razorpay',
            'payment_method' => $paymentMethod,
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'status' => 'completed',
            'paid_at' => Carbon::now(),
        ]);

        if ($coupon && $discount > 0) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'discount_amount' => $discount,
            ]);
        }

        return $subscription;
    }
}
