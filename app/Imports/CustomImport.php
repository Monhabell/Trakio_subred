<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CustomImport implements ToCollection, WithStyles
{
    protected $data;
    protected $styles = [];

    public function collection(Collection $rows)
    {
        $this->data = $rows->toArray();
    }

    public function getData()
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $cellCoordinate = $cell->getCoordinate();
                $fill = $cell->getStyle()->getFill();
                $fillType = $fill->getFillType();
                $startColor = $fill->getStartColor()->getARGB();

                if ($fillType !== null) {
                    $this->styles[$cellCoordinate] = $startColor;
                }

                // Debugging information
                echo "Cell: $cellCoordinate, Fill Type: $fillType, Start Color: $startColor\n";
            }
        }
    }

    public function getStyles()
    {
        return $this->styles;
    }
}
