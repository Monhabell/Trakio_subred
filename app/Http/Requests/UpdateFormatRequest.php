<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormatRequest extends FormRequest
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
        $formatId = $this->route('format')?->id;
        return [
            'environment_id' => 'required|integer|exists:environments,id',
            'name' => ['required','string','max:255',"unique:formats,name,{$formatId}"],
            'header_time' => 'required|numeric',
            'body_time' => 'required|numeric',
            'format_id_sds' => 'required|array',
            'format_id_sds.*' => 'integer|exists:formats_sds,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Por favor ingrese un nombre para el formato',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'header_time.required' => 'Por favor ingrese el tiempo de encabezado',
            'header_time.numeric' => 'El tiempo de encabezado debe ser un número',
            'header_time.double' => 'El tiempo de encabezado debe ser un número decimal',
            'body_time.required' => 'Por favor ingrese el tiempo por cada individuo/seguimiento',
            'body_time.numeric' => 'El tiempo del individuo/seguimiento debe ser un número',
            'body_time.double' => 'El tiempo del individuo/seguimiento debe ser un número decimal',
            'environment_id.integer' => 'El entorno debe ser un número entero',
            'environment_id.exists' => 'El entorno seleccionado no existe',
            'format_id_sds.required' => 'Por favor seleccione al menos un formato SDS',
            'format_id_sds.array' => 'Los formatos SDS deben ser un array',
            'format_id_sds.*.integer' => 'Los IDs de los formatos SDS deben ser números enteros',
            'format_id_sds.*.exists' => 'El formato SDS seleccionado no existe',


        ];
    }
}
