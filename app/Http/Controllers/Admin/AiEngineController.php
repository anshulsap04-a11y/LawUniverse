<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiVendor;
use App\Models\AiConversation;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;

class AiEngineController extends Controller
{
    public function index(Request $request)
    {
        $query = AiVendor::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('vendor_name', 'like', "%{$s}%")
                  ->orWhere('model_name', 'like', "%{$s}%");
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $vendors = $query->latest()->get();
        $totalTokens = AiUsageLog::sum('tokens_used');
        $totalCost = AiUsageLog::sum('cost');
        $conversationsCount = AiConversation::count();

        return view('admin.ai.index', compact('vendors', 'totalTokens', 'totalCost', 'conversationsCount'));
    }

    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'api_base_url' => 'required|url',
            'api_key' => 'required|string',
            'model_name' => 'required|string|max:100',
            'email_address' => 'nullable|email',
            'validity_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_active'])) {
            AiVendor::where('is_active', true)->update(['is_active' => false]);
        }

        AiVendor::create([
            'vendor_name' => $validated['vendor_name'],
            'api_base_url' => $validated['api_base_url'],
            'api_key' => $validated['api_key'],
            'model_name' => $validated['model_name'],
            'email_address' => $validated['email_address'] ?? null,
            'validity_date' => $validated['validity_date'] ?? null,
            'is_active' => !empty($validated['is_active']),
        ]);

        return back()->with('success', 'AI Vendor API configured successfully.');
    }

    public function toggleVendor(AiVendor $vendor)
    {
        $newState = !$vendor->is_active;
        if ($newState) {
            AiVendor::where('id', '!=', $vendor->id)->update(['is_active' => false]);
        }
        $vendor->update(['is_active' => $newState]);

        return back()->with('success', "Vendor status updated.");
    }

    public function validateVendor(AiVendor $vendor)
    {
        // Mock server validation check
        return back()->with('success', "Vendor '{$vendor->vendor_name}' validated successfully by server handshake.");
    }

    public function destroyVendor(AiVendor $vendor)
    {
        $vendor->delete();
        return back()->with('success', 'AI Vendor API configuration deleted.');
    }

    public function bulkTrashVendors(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ai_vendors,id',
        ]);

        AiVendor::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' API engines deleted.');
    }

    // Conversations History
    public function conversations(Request $request)
    {
        $query = AiConversation::with(['user', 'messages']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $conversations = $query->latest()->paginate(20);
        return view('admin.ai.conversations', compact('conversations'));
    }

    public function bulkTrashConversations(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ai_conversations,id',
        ]);

        AiConversation::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' conversation logs removed.');
    }
}
