<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class avanceDigitacion implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $datos;

    public function __construct($datos)
    {
        // Convertimos el objeto procesado en una colección plana para el Excel
        $this->datos = collect($datos)->values();
    }

    public function collection()
    {
        return $this->datos;
    }

    // Definir los encabezados de la tabla
    public function headings(): array
    {
        return [
            'ENTORNO',
            'NOMBRE DEL FORMULARIO',
            'META MENSUAL',
            'SEMANA 1',
            'SEMANA 2',
            'SEMANA 3',
            'SEMANA 4',
            'SEMANA 5'
        ];
    }

    // Aplicar estilos (Colores, bordes, negritas)
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila 1 (Encabezados)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2F5597'] // Azul profesional
                ],
                'alignment' => ['horizontal' => 'center']
            ],
            // Bordes para toda la tabla (asumiendo que hay datos)
            'A1:H' . ($this->datos->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
