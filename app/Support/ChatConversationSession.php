<?php

namespace App\Support;

/**
 * Named wrapper around the raw `session()` keys used to drive the chatbot's
 * multi-turn conversational flows (package assignment, ficha correction,
 * knowledge learning). Centralizes key names so they aren't scattered as
 * magic strings across the controller/services.
 */
class ChatConversationSession
{
    private const SESSION_HANDLERS = [
        'new_patterns' => 'new_patterns',
        'respuesta_New_conocimiento' => 'respuesta_New_conocimiento',
        'confirm_number' => 'confirm_number',
        'waiting_for_incorrect_number' => 'waiting_for_incorrect_number',
        'yes_no' => 'yes_no',
        'numnero_correct' => 'numnero_correct',
        'Respuesta_pkg' => 'Respuesta_pkg',
    ];

    public function activeHandlerKey(): ?string
    {
        foreach (self::SESSION_HANDLERS as $key) {
            if (session($key)) {
                return $key;
            }
        }

        return null;
    }

    // --- Package assignment flow ---

    public function startPackageAssignment(): void
    {
        session(['Respuesta_pkg' => true]);
    }

    public function isAwaitingPackageInput(): bool
    {
        return (bool) session('Respuesta_pkg');
    }

    public function clearPackageAssignment(): void
    {
        session()->forget('Respuesta_pkg');
    }

    // --- Correction flow ---

    public function startCorrection(): void
    {
        session(['waiting_for_incorrect_number' => true]);
    }

    public function isAwaitingIncorrectNumber(): bool
    {
        return (bool) session('waiting_for_incorrect_number');
    }

    public function isAwaitingConfirmation(): bool
    {
        return (bool) session('confirm_number');
    }

    public function isAwaitingYesNo(): bool
    {
        return (bool) session('yes_no');
    }

    public function isAwaitingCorrectNumber(): bool
    {
        return (bool) session('numnero_correct');
    }

    public function setSearchResults(string $incorrectFileNumber, array $options): void
    {
        session(['waiting_for_incorrect_number' => false]);
        session(['incorrect_file_number' => $incorrectFileNumber]);
        session(['search_results_options' => $options]);
        session(['confirm_number' => true]);
    }

    public function searchResultOptions(): array
    {
        return session('search_results_options', []);
    }

    public function confirmSelection(int $recordId): void
    {
        session(['selected_record_id' => $recordId]);
        session(['confirm_number' => false]);
        session(['yes_no' => true]);
    }

    public function acceptCorrection(): void
    {
        session(['yes_no' => false]);
        session(['numnero_correct' => true]);
    }

    public function selectedRecordId(): ?int
    {
        return session('selected_record_id');
    }

    public function incorrectFileNumber(): ?string
    {
        return session('incorrect_file_number');
    }

    public function clearCorrection(): void
    {
        session()->forget([
            'waiting_for_incorrect_number',
            'incorrect_file_number',
            'confirm_number',
            'yes_no',
            'numnero_correct',
            'selected_record_id',
            'search_results_options',
        ]);
    }

    // --- Knowledge learning flow ---

    public function startNewKnowledge(): void
    {
        session(['new_patterns' => true]);
    }

    public function isAwaitingPatterns(): bool
    {
        return (bool) session('new_patterns');
    }

    public function isAwaitingKnowledgeResponse(): bool
    {
        return (bool) session('respuesta_New_conocimiento');
    }

    public function setPatterns(array $patterns): void
    {
        session(['respuest_patterns' => $patterns]);
        session(['new_patterns' => false]);
        session(['respuesta_New_conocimiento' => true]);
    }

    public function patterns(): array
    {
        return session('respuest_patterns', []);
    }

    public function clearKnowledgeLearning(): void
    {
        session()->forget(['new_patterns', 'respuesta_New_conocimiento', 'respuest_patterns']);
    }

    // --- Conversation context / history ---

    public function appendUserContext(int $userId, string $message): void
    {
        $contextKey = 'user_context_' . $userId;
        $context = session($contextKey, []);

        $context[] = [
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if (count($context) > 5) {
            $context = array_slice($context, -5);
        }

        session([$contextKey => $context]);
    }

    public function conversationHistory(int $userId): array
    {
        return session('conversation_history_' . $userId, []);
    }

    public function setConversationHistory(int $userId, array $history): void
    {
        session(['conversation_history_' . $userId => $history]);
    }
}
