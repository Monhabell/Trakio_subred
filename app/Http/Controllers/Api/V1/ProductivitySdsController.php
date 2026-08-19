<?php

namespace App\Http\Controllers\Api\V1;

//Modelos

use App\Http\Controllers\Controller;
use App\Models\Productivity_sds;
use App\Models\Reception;

// Facades
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductivitySdsController extends Controller
{
    public function store(Request $request)
    {
        $id_format_sds = $request->input('id_sds');
        $no_ficha = $request->input('no_ficha');
        $fecha_intervencion = $request->input('fecha_intervencion');

        $fecha_intervencion = Carbon::createFromFormat('d/m/Y', $fecha_intervencion)->format('Y-m-d');

        if ($no_ficha == 0) {
            return response()->json(['message' => 'No puedes reportar la ficha 0 en productividad'], 404);
        }

        $recep = Reception::with(['bases' => function ($query) use ($id_format_sds) {
            $query->with(['format_sds' => function ($query) use ($id_format_sds) {
                $query->where('format_sds_id', $id_format_sds);
            }]);
        }])->where('file_number', $no_ficha)->first();

        if(!$recep){
            return response()->json(['message' => 'No puedes reportar una ficha que no está recepción'], 404);
        }

        // Buscar el registro existente
        $productivitySd = Productivity_sds::where('no_ficha', $no_ficha)->where('id_sds', $id_format_sds)->first();

        if ($productivitySd) {
            $productivitySd->update([
                'updated_at' => now()
            ]);
            return response()->json(['message' => 'Ya puedes reportar la ficha '. $no_ficha.'en productividad'], 200);
        }

        // Crear un nuevo registro
        $productivitySd = new Productivity_sds();
        $productivitySd->id_sds = $id_format_sds;
        $productivitySd->no_ficha = $no_ficha;
        $productivitySd->created_at = now();
        $productivitySd->updated_at = now();
        $productivitySd->save();
        
        DB::disconnect('mysql');
    
        return response()->json(['message' => 'Ya puedes reportar la ficha ' . $no_ficha . ' en productividad'], 200);
    }

    public function storeProductivitySds(Request $request)
    {
       
        try {
            $data = $request->validate([
                'sds_id' => 'required|integer',
                'section_id' => 'required|integer',
                'file_number' => 'required|integer',
                'calculation_time' => 'required|numeric',
                'inactivity_time' => 'required|numeric',
                'user_sds' => 'required|string',
                'pages' => 'nullable|integer',
                'active_page' => 'nullable|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
        }

        try {
            $isOnReception = Reception::with(['bases' => function ($query) use ($data) {
                $query->with(['format_sds' => function ($query) use ($data) {
                    $query->where('format_sds_id', $data['sds_id']);
                }]);
            }])->where('file_number', $data['file_number'])->first();

            if(!$isOnReception){
                return response()->json(['message' => 'Error, la ficha no ha sido recepcionada'], 200);
            }

            $updateExists = Productivity_sds::where('sds_id', $data['sds_id'])
                ->where('file_number', $data['file_number'])
                ->whereNull('dig_id')
                ->update([
                    'calculation_time' => $data['calculation_time'],
                    'inactivity_time' => $data['inactivity_time'],
                    'user_sds' => $data['user_sds'],
                    'pages' => $data['pages'] ?? null,
                    'active_page' => $data['active_page'] ?? null,
                    'updated_at' => now(),
                ]);

            if(!$updateExists){
                Productivity_sds::insert([
                    'sds_id' => $data['sds_id'],
                    'section_id' => $data['section_id'],
                    'file_number' => $data['file_number'],
                    'calculation_time' => $data['calculation_time'],
                    'inactivity_time' => $data['inactivity_time'] < 0 ? 0 : $data['inactivity_time'],
                    'user_sds' => $data['user_sds'],
                    'pages' => $data['pages'] ?? null,
                    'active_page' => $data['active_page'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['message' => 'Datos de productividad guardados correctamente'], 201);
        } catch (\Exception $e) {
            Log::error('Error al guardar datos de productividad: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar los datos de productividad'], 500);
        }
    }
}