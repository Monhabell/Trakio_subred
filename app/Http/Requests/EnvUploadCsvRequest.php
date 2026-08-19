<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnvUploadCsvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:csv,txt|max:2048',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $file = $this->file('file');

            if (!$file) {
                return;
            }

            $data = array_map('str_getcsv', file($file->getRealPath()));

            // Omitir primera fila
            foreach (array_slice($data, 1) as $index => $row) {
                $data_columns = explode(';', $row[0] ?? '');

                // Validar número de columnas
                if (count($data_columns) < 4) {
                    $validator->errors()->add('file', "La fila ".($index+2).": No tiene las 4 columnas requeridas");
                    continue;
                }

                // Columna 0: solo números
                if (!ctype_digit($data_columns[0])) {
                    $validator->errors()->add('file', "Fila ".($index+2).": La primera columna debe contener solo números");
                }

                // Columna 1: debe existir en formats
                $formatExists = DB::table('formats')->where('id', $data_columns[1])->exists();
                if (!$formatExists) {
                    $validator->errors()->add('file', "Fila ".($index+2).": El formato con ID {$data_columns[1]} no existe");
                }

                // Columna 2: cantidad, número entero positivo
                if (!ctype_digit($data_columns[2]) || intval($data_columns[2]) <= 0) {
                    $validator->errors()->add('file', "Fila ".($index+2).": La cantidad debe ser un número entero positivo");
                }

                // Columna 3: fecha válida con formato YYYY-MM-DD y no mayor a hoy
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_columns[3])) {
                    $validator->errors()->add('file', "Fila ".($index+2).": La fecha de intervención debe tener el formato AAAA-MM-DD (ej: 2025-02-10)");
                } else {
                    try {
                        $fecha = Carbon::createFromFormat('Y-m-d', $data_columns[3]);
                        if ($fecha->greaterThan(Carbon::today())) {
                            $validator->errors()->add('file', "Fila ".($index+2).": La fecha de intervención no puede ser mayor a hoy");
                        }
                    } catch (\Exception $e) {
                        Log::error($e);
                        $validator->errors()->add('file', "Fila ".($index+2).": La fecha intervención no es válida");
                    }
                }

                // Columna 4: entorno debe coincidir con el entorno del número de ficha
                if(intval(substr($data_columns[0], 4, 1)) !== intval($data_columns[4])){
                    $validator->errors()->add('file', "Fila ".($index+2).": El entorno del número de ficha no coincide con el entorno indicado");
                }

                // Validar la localidad según los dos primeros dígitos del número de ficha
                $localidadId = intval(substr($data_columns[0], 1, 2));
                $localidadExists = DB::table('localidades')->where('identificador', $localidadId)->exists();
                if (!$localidadExists) {
                    $validator->errors()->add('file', "Fila ".($index+2).": La localidad número {$localidadId} no existe");
                }
            }
        });
    }
}
