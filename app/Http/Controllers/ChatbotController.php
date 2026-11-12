<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function ask(): View
    {
        return view('chatbot.ask');
    }

    public function history(): View
    {
        return view('chatbot.history');
    }
}
