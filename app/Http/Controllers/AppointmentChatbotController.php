<?php

namespace App\Http\Controllers;

use App\Ai\Agents\TemujanjiChatbot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AppointmentChatbotController extends Controller
{
    public function index() {
        return view('chatbot.index');
    }

    public function sendMessage(Request $request) {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

      try {
            $response = (new TemujanjiChatbot())->forUser(Auth::user())
                        ->prompt($validated['message'], provider: 'openai', model : config('ai.models.text'));

            return response()->json(['success' => true, 'response' => $response]);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Failed to get response from AI: ' . $e->getMessage()], 500);
        }

    }
}
