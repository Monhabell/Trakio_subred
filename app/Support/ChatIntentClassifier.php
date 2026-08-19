<?php

namespace App\Support;

class ChatIntentClassifier
{
    private const PACKAGE_PATTERNS = [
        '/asignar paquete/i',
        '/asigname un paquete/i',
        '/quiero un paquete/i',
        '/dame un paquete/i',
        '/paquete para digitar/i',
        '/asignar pkg/i',
        '/asignar pack/i',
        '/necesito un paquete/i',
        '/puedes asignarme/i',
        '/asignar (?:el |un )?paquete/i',
    ];

    private const CORRECTION_PATTERNS = [
        '/corregir.*ficha/i',
        '/cambiar.*(?:numero.*ficha|ficha.*numero)/i',
        '/modificar.*ficha/i',
        '/numero.*ficha.*incorrecto/i',
        '/ficha.*(?:mal|incorrecta)/i',
        '/corregir.*numero.*ficha/i',
        '/error.*(?:en.*)?ficha/i',
    ];

    private const QUERY_PATTERNS = [
        '/consultar.*ficha/i',
        '/busc.*ficha/i',
        '/informacion.*ficha/i',
        '/estado.*ficha/i',
        '/ver.*ficha/i',
        '/detalles.*ficha/i',
        '/ficha.*(?:numero|estado|informacion|datos|detalles)/i',
        '/(?:quiero|necesito|puedes?|puede|dame).*(?:ver|saber|buscar|encontrar|consultar|informacion|datos).*ficha/i',
        '/(?:ver|saber|conocer).*(?:estado|informacion|datos).*ficha/i',
    ];

    private const KNOWLEDGE_PATTERNS = [
        '/enseñame/i',
        '/aprender/i',
        '/nuevo conocimiento/i',
        '/agregar respuesta/i',
        '/enseñarle algo/i',
    ];

    private const DOCUMENT_KEYWORDS = [
        'gesi', 'documento', 'pdf', 'entregable', 'lineamiento', 'norma',
        'procedimiento', 'guia', 'manual', 'instruccion', 'protocolo',
        'estandar', 'requisito', 'especificacion', 'politica', 'formatos',
        'plantilla', 'documentacion', 'que es', 'qué es', 'como funciona',
        'cómo funciona', 'para que sirve', 'para qué sirve', 'definicion',
        'definición', 'concepto', 'funcionalidad', 'caracteristica',
        'característica',
    ];

    private const SPECIFIC_DOCUMENT_QUERIES = [
        'que es gesi', 'qué es gesi', 'que es el gesi', 'qué es el gesi',
        'para que sirve gesi', 'para qué sirve gesi', 'como funciona gesi',
        'cómo funciona gesi', 'definicion de gesi', 'definición de gesi',
    ];

    public function detectDirect(string $message): ?string
    {
        $normalized = $this->normalize($message);

        if ($this->matchesAny(self::PACKAGE_PATTERNS, $normalized)) {
            return 'asignar_paquete';
        }

        if ($this->matchesAny(self::CORRECTION_PATTERNS, $normalized)) {
            return 'corregir_ficha';
        }

        if ($this->matchesAny(self::QUERY_PATTERNS, $normalized)) {
            return 'consultar_ficha';
        }

        if ($this->matchesAny(self::KNOWLEDGE_PATTERNS, $normalized)) {
            return 'nuevo_conocimiento';
        }

        if (preg_match('/\b(\d{6,})\b/', $normalized)) {
            return 'consultar_ficha_numerica';
        }

        return null;
    }

    public function isDocumentQuery(string $message): bool
    {
        $normalized = $this->normalize($message);

        foreach (self::SPECIFIC_DOCUMENT_QUERIES as $query) {
            if (str_contains($normalized, $query)) {
                return true;
            }
        }

        $keywordCount = 0;
        foreach (self::DOCUMENT_KEYWORDS as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $keywordCount++;
            }
        }

        return $keywordCount >= 1 || in_array($normalized, self::SPECIFIC_DOCUMENT_QUERIES, true);
    }

    public function normalize(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('/[^\w\s]/', '', $text);

        return strtolower(trim($text));
    }

    private function matchesAny(array $patterns, string $normalizedMessage): bool
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $normalizedMessage)) {
                return true;
            }
        }

        return false;
    }
}
