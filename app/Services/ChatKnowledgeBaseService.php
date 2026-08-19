<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Wraps the `intents/intent_responses.json` file that backs the chatbot's
 * learnable knowledge base.
 */
class ChatKnowledgeBaseService
{
    private const STORAGE_PATH = 'intents/intent_responses.json';

    private const CACHE_KEY = 'intent_responses';

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            if (Storage::exists(self::STORAGE_PATH)) {
                return json_decode(Storage::get(self::STORAGE_PATH), true) ?? [];
            }

            return [];
        });
    }

    public function learn(array $patterns, string $response, int $userId): void
    {
        $intents = $this->all();

        $intents['intent_' . time()] = [
            'patterns' => $patterns,
            'response' => $response,
            'created_by' => $userId,
            'created_at' => now()->toISOString(),
        ];

        Storage::put(self::STORAGE_PATH, json_encode($intents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        Cache::forget(self::CACHE_KEY);
    }
}
