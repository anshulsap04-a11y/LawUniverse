<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiVendor;
use App\Models\AiConversation;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;

class AiEngineController extends Controller
{
    /**
     * AI Vendor configuration & API management.
     */
    public function index()
    {
        $vendors = AiVendor::all();
        $totalTokens = AiUsageLog::sum('tokens_used');
        $totalCost = AiUsageLog::sum('cost');
        $conversationsCount = AiConversation::count();

        return view('admin.ai.index', compact('vendors', 'totalTokens', 'totalCost', 'conversationsCount'));
    }

    /**
     * Store new AI Vendor configuration.
     */
    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'api_endpoint' => 'required|url',
            'api_key' => 'required|string',
            'model_name' => 'required|string|max:100',
            'cost_per_1k_tokens' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_active'])) {
            // Deactivate others if this is set active
            AiVendor::where('is_active', true)->update(['is_active' => false]);
        }

        AiVendor::create([
            'name' => $validated['name'],
            'api_endpoint' => $validated['api_endpoint'],
            'api_key' => $validated['api_key'],
            'model_name' => $validated['model_name'],
            'cost_per_1k_tokens' => $validated['cost_per_1k_tokens'],
            'is_active' => !empty($validated['is_active']),
        ]);

        return back()->with('success', 'AI Vendor API configured successfully.');
    }

    /**
     * Toggle active vendor.
     */
    public function toggleVendor(AiVendor $vendor)
    {
        AiVendor::where('id', '!=', $vendor->id)->update(['is_active' => false]);
        $vendor->update(['is_active' => true]);

        return back()->with('success', "Active AI Vendor switched to {$vendor->name}.");
    }

    /**
     * View AI conversation history.
     */
    public function conversations()
    {
        $conversations = AiConversation::with(['user', 'messages'])->latest()->paginate(20);
        return view('admin.ai.conversations', compact('conversations'));
    }
}
