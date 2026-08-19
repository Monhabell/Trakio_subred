<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Agregado para que las columnas se ajusten solas
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DigitadoresExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($data, $fechaInicio, $fechaFin)
    {
        $this->data = $data;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        /**
         * Como ya mapeamos los datos en el controlador, 
         * $this->data ya tiene la estructura final.
         * Solo lo retornamos.
         */
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            "Digitador", 
            "Entorno", 
            "Horas Totales", 
            "Meta destinada",
            "% Cumplimiento",
            "Periodo Reportado"
        ];
    }

    public function title(): string
    {
        return 'Reporte de Productividad';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Fila 1: Fondo rojo (C00000) y letras blancas negritas
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF']
                ], 
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                    'startColor' => ['rgb' => 'C00000']
                ]
            ],
        ];
    }
}