<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class PdfKnowledgeService
{
    private const MAX_TEXT_LENGTH = 20000;

    public function extractAllText(): ?string
    {
        $directory = storage_path('app/public/');
        $pdfFiles = glob($directory . '*.pdf');

        if (empty($pdfFiles)) {
            return null;
        }

        $parser = new Parser();
        $allText = '';

        foreach ($pdfFiles as $file) {
            if (!file_exists($file)) {
                continue;
            }

            try {
                $pdf = $parser->parseFile($file);
                $text = $pdf->getText();

                if (!empty(trim($text))) {
                    $allText .= "\n\n===== [Documento: " . basename($file) . "] =====\n" . $text;
                }
            } catch (\Exception $e) {
                Log::warning('Error al leer PDF: ' . $file . ' -> ' . $e->getMessage());
            }
        }

        if (empty(trim($allText))) {
            Log::warning('No se pudo extraer texto de ningún PDF.');

            return null;
        }

        if (strlen($allText) > self::MAX_TEXT_LENGTH) {
            $allText = substr($allText, 0, self::MAX_TEXT_LENGTH) . '... [texto truncado para análisis]';
        }

        return $allText;
    }
}
