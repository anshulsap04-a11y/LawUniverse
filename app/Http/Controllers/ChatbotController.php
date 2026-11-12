<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\AiAssistantService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show legal AI chat interface.
     */
    public function ask(Request $request): View
    {
        $user = auth()->user();
        $conversations = AiConversation::where('user_id', $user->id)->latest()->get();

        $activeConversation = null;
        if ($request->filled('conversation_id')) {
            $activeConversation = AiConversation::where('id', $request->conversation_id)
                ->where('user_id', $user->id)
                ->with('messages')
                ->first();
        }

        return view('chatbot.ask', compact('conversations', 'activeConversation'));
    }

    /**
     * Send prompt to legal AI assistant.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:3|max:2000',
            'conversation_id' => 'nullable|exists:ai_conversations,id',
        ]);

        $result = $this->aiService->ask(
            auth()->user(),
            $validated['prompt'],
            $validated['conversation_id'] ?? null
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('chatbot.ask', ['conversation_id' => $result['conversation_id']]);
    }

    /**
     * View history of conversations.
     */
    public function history(): View
    {
        $conversations = AiConversation::where('user_id', auth()->id())
            ->withCount('messages')
            ->latest()
            ->paginate(15);

        return view('chatbot.history', compact('conversations'));
    }
}
