<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected $spreadsheetId;
    protected $service;

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
        $this->service = $this->connectToGoogleSheets();
    }

    private function connectToGoogleSheets()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Sheets::SPREADSHEETS);
        return new Sheets($client);
    }

    public function obtenerErroresPorUsuario($nombreUsuario, $entorno)
    {
        $rangos = [
            ["range" => "$entorno!A1:GZ600", "offset" => 0],
            ["range" => "$entorno!A601:GZ900", "offset" => 600],
            ["range" => "$entorno!A901:GZ1000", "offset" => 900]
        ];

        $values = [];
        $backgroundColors = [];
        $nombreUsuario = preg_replace('/\s+/', ' ', trim($nombreUsuario));

        foreach ($rangos as $rangoConfig) {
            $range = $rangoConfig["range"];
            $offset = $rangoConfig["offset"];

            try {
                // Obtener valores y formatos en una sola llamada
                $response = $this->service->spreadsheets->get($this->spreadsheetId, [
                    'ranges' => [$range],
                    'fields' => 'sheets(data(rowData(values(formattedValue,userEnteredFormat.backgroundColor))))'
                ]);

                $sheet = $response->getSheets()[0] ?? null;
                if (!$sheet || !$sheet->getData())
                    continue;

                $gridData = $sheet->getData()[0];
                $rowData = $gridData->getRowData() ?? [];

                foreach ($rowData as $rowIndex => $row) {
                    $realRowIndex = $rowIndex + $offset;
                    $values[$realRowIndex] = [];

                    $cellData = $row->getValues() ?? [];
                    foreach ($cellData as $colIndex => $cell) {
                        // Guardar valor
                        $values[$realRowIndex][$colIndex] = $cell->getFormattedValue() ?? '';

                        // Guardar color
                        if ($cell->getUserEnteredFormat() && $cell->getUserEnteredFormat()->getBackgroundColor()) {
                            $color = $cell->getUserEnteredFormat()->getBackgroundColor();
                            $backgroundColors[$realRowIndex][$colIndex] = sprintf(
                                "rgb(%.1f, %.1f, %.1f)",
                                (int) round(($color->getRed() ?? 0) * 255), // color Red
                                (int) round(($color->getGreen() ?? 0) * 255), // 0
                                (int) round(($color->getBlue() ?? 0) * 255) // 0
                            );
                        }


                    }
                }

            } catch (\Exception $e) {
                Log::error("Error al obtener datos de Google Sheets: " . $e->getMessage());
                continue;
            }
        }

        // Procesamiento de errores por usuario
        $erroresPorSeccion = [];
        $seccion = null;
        $encabezados = [];

        if (!empty($values)) {
            foreach ($values as $rowIndex => $row) {
                if (empty(array_filter($row)))
                    continue;

                if (isset($row[1]) && strtolower($row[1]) === 'corregido') {
                    $seccion = $values[$rowIndex - 1][1] ?? 'Sin Nombre';
                    $encabezados = $values[$rowIndex] ?? [];
                    continue;
                }

                if (isset($row[0]) && strtolower($row[0]) === strtolower($nombreUsuario)) {
                    if (!isset($erroresPorSeccion[$seccion])) {
                        $erroresPorSeccion[$seccion] = [
                            'encabezados' => $encabezados,
                            'datos' => []
                        ];
                    }

                    $filaConColor = [];
                    foreach ($row as $colIndex => $value) {
                        if ($colIndex != 0) {
                            $filaConColor[] = [
                                'valor' => $value,
                                'color' => $backgroundColors[$rowIndex][$colIndex] ?? 'rgba(100, 100, 100, 0.45)',
                                'fila' => $rowIndex + 1,
                                'entorno' => $entorno
                            ];
                        }
                    }

                    $erroresPorSeccion[$seccion]['datos'][] = $filaConColor;
                }
            }
        }

        return $erroresPorSeccion ?: [];
    }
}