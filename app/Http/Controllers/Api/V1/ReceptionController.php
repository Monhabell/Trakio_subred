<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


// Recursos
use App\Http\Resources\V1\ReceptionResourse;
use App\Http\Resources\V1\ReceptionCollection;

// Facades
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Modelos
use App\Models\Reception;

class ReceptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receptions = Reception::with('bases', 'environment_file', 'user', 'packages')->latest()->paginate();

        return new ReceptionCollection($receptions);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reception $reception)
    {
        $reception = $reception->load('bases', 'environment_file', 'user', 'packages');

        return new ReceptionResourse($reception);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $reception = Reception::create($request->all());
        
        return response()->json(new ReceptionResourse($reception), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
