<?php

namespace App\Http\Controllers;

use App\Services\Rag\ChatService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request, SettingsService $settings, ChatService $chatService): JsonResponse
    {
        if (! $settings->isChatboxEnabled()) {
            return response()->json([
                'message' => 'The AI chatbox is currently disabled.',
            ], 403);
        }

        if (! $settings->isOpenAiConfigured()) {
            return response()->json([
                'message' => 'The AI chatbox is not configured. Please contact an administrator.',
            ], 503);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:'.config('rag.max_message_length', 1000),
        ]);

        try {
            $result = $chatService->respond($validated['message']);

            return response()->json($result);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Something went wrong while generating a response. Please try again.',
            ], 500);
        }
    }
}
