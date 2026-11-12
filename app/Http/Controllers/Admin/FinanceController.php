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
    public function statistics()
    {
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $successfulTxns = Transaction::where('status', 'completed')->count();
        $activeSubs = Subscription::where('status', 'active')->count();
        $totalCoupons = Coupon::where('status', 'active')->count();

        $recentTransactions = Transaction::with(['user', 'package'])->latest()->take(10)->get();

        return view('admin.finance.statistics', compact('totalRevenue', 'successfulTxns', 'activeSubs', 'totalCoupons', 'recentTransactions'));
    }

    // Packages
    public function packages(Request $request)
    {
        $query = Package::with('features');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $packages = $query->latest()->paginate(15);
        $features = Feature::all();

        return view('admin.finance.packages', compact('packages', 'features'));
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'current_price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
            'selected_features' => 'nullable|array',
        ]);

        $package = Package::create([
            'plan_code' => 'PKG-' . strtoupper(Str::random(6)),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'current_price' => $validated['current_price'],
            'duration_days' => $validated['duration_days'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['selected_features'])) {
            $package->features()->sync($validated['selected_features']);
        }

        return back()->with('success', 'Subscription plan created successfully.');
    }

    public function bulkTrashPackages(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:packages,id',
        ]);

        Package::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' subscription plans deleted.');
    }

    // Features
    public function features(Request $request)
    {
        $query = Feature::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('feature_type')) {
            $query->where('feature_type', $request->feature_type);
        }

        $features = $query->latest()->paginate(20);
        return view('admin.finance.features', compact('features'));
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:features,slug',
            'feature_type' => 'required|in:boolean,usage_limit,download_limit,time_access',
            'description' => 'nullable|string',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        Feature::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'feature_type' => $validated['feature_type'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Platform capability feature added.');
    }

    public function bulkTrashFeatures(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:features,id',
        ]);

        Feature::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' features deleted.');
    }

    // Transactions
    public function transactions(Request $request)
    {
        $query = Transaction::with(['user', 'package']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('transaction_code', 'like', "%{$s}%")
                  ->orWhere('invoice_number', 'like', "%{$s}%");
        }

        $transactions = $query->latest()->paginate(20);
        return view('admin.finance.transactions', compact('transactions'));
    }

    // Subscriptions
    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::with(['user', 'package'])->latest()->paginate(20);
        return view('admin.finance.subscriptions', compact('subscriptions'));
    }

    // Coupons
    public function coupons(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('coupon_code', 'like', "%{$s}%")
                  ->orWhere('coupon_name', 'like', "%{$s}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $coupons = $query->latest()->paginate(20);
        return view('admin.finance.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:50|unique:coupons,coupon_code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'expiration_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['coupon_code'] = strtoupper(trim($validated['coupon_code']));
        Coupon::create($validated);

        return back()->with('success', 'Promotional discount coupon created.');
    }

    public function toggleCoupon(Coupon $coupon)
    {
        $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->update(['status' => $newStatus]);

        return back()->with('success', "Coupon status toggled to {$newStatus}.");
    }

    public function bulkTrashCoupons(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:coupons,id',
        ]);

        Coupon::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' coupons deleted.');
    }
}
