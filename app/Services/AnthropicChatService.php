<?php

namespace App\Services;

use Anthropic\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Sole owner of the Anthropic SDK client. No other class in the app should
 * instantiate `Anthropic\Client` directly.
 */
class AnthropicChatService
{
    private const MODEL = 'claude-opus-4-8';

    private const VALID_INTENTS = [
        'corregir_ficha',
        'asignar_paquete',
        'nuevo_conocimiento',
        'consultar_documento',
        'consultar_ficha',
        'consultar_usuarios',
        'general_query',
    ];

    private Client $client;

    public function __construct()
    {
        $this->client = new Client(apiKey: env('ANTHROPIC_API_KEY'));
    }

    public function classifyIntent(string $message): string
    {
        $cacheKey = 'intent_' . md5($message);

        return Cache::remember($cacheKey, 300, function () use ($message) {
            try {
                $response = $this->client->messages->create(
                    model: self::MODEL,
                    maxTokens: 20,
                    system: "Analiza la consulta del usuario y clasifícala en una de estas categorías: "
                        . "'corregir_ficha', 'asignar_paquete', 'nuevo_conocimiento', 'consultar_documento', "
                        . "'consultar_ficha', 'consultar_usuarios', 'general_query'. "
                        . "Responde ÚNICAMENTE con el nombre exacto de la categoría más precisa, sin explicaciones.",
                    messages: [
                        ['role' => 'user', 'content' => $message],
                    ],
                );

                $intent = $this->firstTextBlock($response) ?? 'general_query';
                $intent = strtolower(trim($intent, " \t\n\r.\"'"));

                return in_array($intent, self::VALID_INTENTS, true) ? $intent : 'general_query';
            } catch (\Exception $e) {
                Log::error('Error detectando intención: ' . $e->getMessage());

                return 'general_query';
            }
        });
    }

    /**
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function chat(array $messages, string $systemPrompt): string
    {
        $response = $this->client->messages->create(
            model: self::MODEL,
            maxTokens: 2048,
            thinking: ['type' => 'adaptive'],
            system: $systemPrompt,
            messages: $messages,
        );

        return $this->firstTextBlock($response) ?? 'Lo siento, no pude procesar tu mensaje.';
    }

    public function answerFromDocuments(string $documentsText, string $userQuery): ?string
    {
        $response = $this->client->messages->create(
            model: self::MODEL,
            maxTokens: 2048,
            thinking: ['type' => 'adaptive'],
            system: "Eres Tara, una asistente especializada en la documentación del sistema GESI (Gestión de Información SAC).

**INSTRUCCIONES CRÍTICAS:**
1. Responde ÚNICAMENTE con información presente en los documentos.
2. Si no está, responde: 'No encontré información específica sobre esto en la documentación'.
3. Sé clara, organizada y breve (máximo 400 palabras).
4. Usa español y emojis adecuados.

**FORMATO DE RESPUESTA:**
📄 **Información del Documento GESI**
- Usa viñetas (•)
- Si no hay información, dilo claramente",
            messages: [
                [
                    'role' => 'user',
                    'content' => "**DOCUMENTOS DE REFERENCIA:**\n{$documentsText}\n\n**CONSULTA DEL USUARIO:**\n\"{$userQuery}\"",
                ],
            ],
        );

        $content = $this->firstTextBlock($response);

        if ($content === null) {
            throw new \Exception('Respuesta de API inválida');
        }

        $content = trim($content);

        if (
            strpos(strtolower($content), 'no encontré') !== false ||
            strpos(strtolower($content), 'no está en el documento') !== false ||
            strlen($content) < 50
        ) {
            return null;
        }

        return $content;
    }

    private function firstTextBlock($response): ?string
    {
        foreach ($response->content as $block) {
            if ($block->type === 'text') {
                return $block->text;
            }
        }

        return null;
    }
}
