<?php

namespace App\Http\Controllers;

use App\Ai\Agents\DatabaseAssistant;
use App\Ai\Agents\TemujanjiChatbot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AppointmentChatbotController extends Controller
{

    private const BLOCKED_CHAT_PATTERNS = [
        'information_schema',
        'performance_schema',
        'pg_catalog',
        'sqlite_master',
        'describe table',
        'desc ',
        'show tables',
        'show columns',
        'show create table',
        'struktur jadual',
        'struktur database',
        'schema database',
        'metadata database',
        'senarai kolum',
    ];

    public function index() {
        return view('chatbot.index');
    }

    public function sendMessage(Request $request) {

        if(!Auth::user()?->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }


        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if($this->containsBlockedPattern($validated['message'])) {
            return response()->json(['success' => false, 'message' => 'Tak baik tanya macam tu.'], 400);
        }

      try {
            $response = (new DatabaseAssistant())->forUser(Auth::user())
                        ->prompt($validated['message'], provider: 'openai', model : config('ai.models.text'));

            return response()->json(['success' => true, 'response' => $response]);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Failed to get response from AI: ' . $e->getMessage()], 500);
        }

    }

    private function containsBlockedPattern(string $message): bool
    {
        foreach (self::BLOCKED_CHAT_PATTERNS as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }
}
