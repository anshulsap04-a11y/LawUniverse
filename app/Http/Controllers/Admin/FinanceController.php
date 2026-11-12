<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Feature;
use App\Models\Coupon;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    /**
     * Financial statistics & dashboard.
     */
    public function statistics()
    {
        $totalRevenue = Transaction::where('status', 'successful')->sum('amount');
        $successfulTxns = Transaction::where('status', 'successful')->count();
        $activeSubs = Subscription::where('status', 'active')->count();
        $totalCoupons = Coupon::count();

        $recentTransactions = Transaction::with(['user', 'package'])->latest()->take(10)->get();

        return view('admin.finance.statistics', compact('totalRevenue', 'successfulTxns', 'activeSubs', 'totalCoupons', 'recentTransactions'));
    }

    /**
     * Packages listing and creation.
     */
    public function packages()
    {
        $packages = Package::with('features')->get();
        $features = Feature::all();
        return view('admin.finance.packages', compact('packages', 'features'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
            'selected_features' => 'nullable|array',
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_in_days' => $validated['duration_in_days'],
            'is_active' => !empty($validated['is_active']),
        ]);

        if (!empty($validated['selected_features'])) {
            $package->features()->sync($validated['selected_features']);
        }

        return back()->with('success', 'Subscription package created successfully.');
    }

    /**
     * Features management.
     */
    public function features()
    {
        $features = Feature::all();
        return view('admin.finance.features', compact('features'));
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:features,code',
            'description' => 'nullable|string',
        ]);

        Feature::create($validated);
        return back()->with('success', 'Package feature added successfully.');
    }

    /**
     * Transactions listing.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with(['user', 'package']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('transaction_number', 'like', "%{$s}%")
                  ->orWhere('invoice_number', 'like', "%{$s}%");
        }

        $transactions = $query->latest()->paginate(20);
        return view('admin.finance.transactions', compact('transactions'));
    }

    /**
     * Subscriptions listing.
     */
    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::with(['user', 'package'])->latest()->paginate(20);
        return view('admin.finance.subscriptions', compact('subscriptions'));
    }

    /**
     * Coupons management.
     */
    public function coupons()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.finance.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = !empty($validated['is_active']);

        Coupon::create($validated);
        return back()->with('success', 'Promotional coupon code created successfully.');
    }
}
