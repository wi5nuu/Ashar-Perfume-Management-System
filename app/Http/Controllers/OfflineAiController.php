<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\CopilotEngineInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class OfflineAiController extends Controller
{
    private const SESSION_HISTORY = 'ai_chat_history';

    public function __construct(
        private CopilotEngineInterface $engine
    ) {}

    public function chat(Request $request)
    {
        try {
            if (!Gate::check('view_reports')) {
                return response()->json([
                    'reply' => 'Maaf, Anda tidak memiliki izin untuk mengakses asisten digital ini. Silakan hubungi administrator.'
                ], 200);
            }

            $raw = trim($request->input('message', ''));
            if (empty($raw)) {
                return response()->json(['reply' => 'Silakan masukkan pertanyaan Anda.'], 200);
            }

            $history = $request->session()->get(self::SESSION_HISTORY, []);

            $response = $this->engine->handle($raw, [
                'messages' => $history,
                'user_id' => auth()->id(),
            ]);

            $history[] = ['role' => 'user', 'message' => $raw];
            $history[] = ['role' => 'bot', 'message' => $response];
            $history = array_slice($history, -20);
            $request->session()->put(self::SESSION_HISTORY, $history);

            return response()->json(['reply' => $response], 200);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Maaf, terjadi kesalahan sistem. Silakan coba beberapa saat lagi. Jika masalah berlanjut, hubungi administrator.'
            ], 200);
        }
    }
}
