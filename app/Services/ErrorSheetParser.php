<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ErrorSheetParser
{
    /**
     * Columna 1 = número de ficha (fija). Las columnas "Usuario", "Número de usuario/Página"
     * y "Observaciones" no tienen una posición fija en el Excel real, así que se buscan por
     * su encabezado dentro de este rango. Son columnas de contexto: nunca se tratan como
     * campos en error, sin importar si la celda está en rojo.
     */
    private const FICHA_COLUMN_INDEX = 1;
    private const HEADER_SEARCH_RANGE = 6;
    private const USERNAME_HEADER_PATTERN = '/^usuario$/i';
    private const LOCATION_HEADER_PATTERN = '/^(n[uú]mero\s*(de\s*)?usuario|n[uú]m\.?\s*usuario|p[aá]g(ina)?)$/i';
    private const OBSERVATIONS_HEADER_PATTERN = '/^observaci[oó]n(es)?$/i';

    public function parse(string $filePath): array
    {
        $sheet = IOFactory::load($filePath)->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        $usernameColumnIndex = $this->findHeaderColumn($sheet, $highestColumnIndex, self::USERNAME_HEADER_PATTERN);
        $locationColumnIndex = $this->findHeaderColumn($sheet, $highestColumnIndex, self::LOCATION_HEADER_PATTERN);
        $observationsColumnIndex = $this->findHeaderColumn($sheet, $highestColumnIndex, self::OBSERVATIONS_HEADER_PATTERN);
        $contextColumns = [$usernameColumnIndex, $locationColumnIndex, $observationsColumnIndex];

        $fields = [];
        for ($col = self::FICHA_COLUMN_INDEX + 1; $col <= $highestColumnIndex; $col++) {
            if (in_array($col, $contextColumns, true)) {
                continue;
            }
            $label = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
            if ($label !== '') {
                $fields[$col] = $label;
            }
        }

        $parsedRows = [];
        $totalRows = 0;
        $flaggedCount = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $fileNumber = trim((string) $sheet->getCellByColumnAndRow(self::FICHA_COLUMN_INDEX, $row)->getValue());
            if ($fileNumber === '') {
                continue;
            }
            $totalRows++;

            $usernameRaw = $usernameColumnIndex
                ? trim((string) $sheet->getCellByColumnAndRow($usernameColumnIndex, $row)->getValue())
                : '';

            $locationReference = $locationColumnIndex
                ? trim((string) $sheet->getCellByColumnAndRow($locationColumnIndex, $row)->getValue())
                : '';

            $observations = $observationsColumnIndex
                ? trim((string) $sheet->getCellByColumnAndRow($observationsColumnIndex, $row)->getValue())
                : '';

            $flags = [];
            foreach ($fields as $col => $label) {
                $cell = $sheet->getCellByColumnAndRow($col, $row);
                if ($this->isRed($cell)) {
                    $flags[] = [
                        'field_label' => $label,
                        'value' => trim((string) $cell->getValue()),
                    ];
                }
            }

            if (empty($flags)) {
                continue;
            }

            $flaggedCount++;
            $parsedRows[] = [
                'row_number' => $row,
                'file_number' => $fileNumber,
                'username_raw' => $usernameRaw,
                'location_reference' => $locationReference,
                'observations' => $observations,
                'flags' => $flags,
            ];
        }

        return [
            'username_column_found' => $usernameColumnIndex !== null,
            'location_column_found' => $locationColumnIndex !== null,
            'observations_column_found' => $observationsColumnIndex !== null,
            'fields' => array_values($fields),
            'rows' => $parsedRows,
            'stats' => [
                'total_rows' => $totalRows,
                'flagged_rows' => $flaggedCount,
            ],
        ];
    }

    private function findHeaderColumn($sheet, int $highestColumnIndex, string $pattern): ?int
    {
        $lastColumnToSearch = min(self::HEADER_SEARCH_RANGE, $highestColumnIndex);

        for ($col = self::FICHA_COLUMN_INDEX + 1; $col <= $lastColumnToSearch; $col++) {
            $label = trim((string) $sheet->getCellByColumnAndRow($col, 1)->getValue());
            if (preg_match($pattern, $label)) {
                return $col;
            }
        }

        return null;
    }

    private function isRed(Cell $cell): bool
    {
        $fill = $cell->getStyle()->getFill();

        if ($fill->getFillType() !== Fill::FILL_SOLID) {
            return false;
        }

        $argb = $fill->getStartColor()->getARGB();

        return $argb && strtoupper(substr($argb, -6)) === 'FF0000';
    }
}
