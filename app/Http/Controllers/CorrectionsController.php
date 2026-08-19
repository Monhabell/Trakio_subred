<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Correction;

// Requests personalizados
use App\Http\Requests\StoreCorrectionRequest;

// Facades
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CorrectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $corrections = Correction::with(['user', 'receptions.bases', 'receptions.environment_file'])
                        ->orderBy('id', 'DESC')->get();

        return view('adm.corrections', [
            'corrections' => $corrections
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCorrectionRequest $request)
    {        
        try {
            Correction::create([
                'id_receptions' => $request->idProduct,
                'id_user' => Auth::id(),
                'tipo_cambio' => $request->acciones,
                'id_reg' => $request->registro_id,
                'no_pag' => $request->no_pagina,
                'nuevo_numero' => $request->No_num,
                'nueva_fecha' => $request->fecha
            ]);

            return response()->json(['success' => 'Corrección enviada exitosamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error en la base de datos: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un problema al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $query = Correction::with(['user', 'receptions.bases', 'receptions.environment_file']);

        if($request->status == '1'){
            $query->whereNotNull('corrected');
        }elseif($request->status == 'null'){
            $query->whereNull('corrected');
        }

        if($request->file_number != ''){
            $query->whereHas('receptions', function($query) use ($request){
                $query->where('file_number', 'LIKE', '%'.$request->file_number.'%');
            });
        }

        $corrections = $query->orderBy('id', 'DESC')->limit(15)->get(); 
        
        //$corrections->toSql();

        return response()->json([
            'corrections' => $corrections
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Correction $correction)
    {
        $request->validate([
            'status' => 'nullable|boolean'
        ]);

        try {
            $new_status = $request->input('status') ? $request->input('status') : null;
            
            $correction->update([
                'corrected' => $new_status
            ]);

            return response()->json(['message', 'Corrección actualizada']);
        } catch (\Exception $e) {
            return response()->json(['message', 'No se pudo actualizar el estado de la corrección']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Correction $correction)
    {
        try {
            $result = $correction->delete();
            $message = $result ? 'Corrección eliminada exitosamente' : 'No se pudo eliminar la corrección.';

            return response()->json([
                'success' => true,
                'message' => $message
            ], 200);
        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar la corrección.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}