<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

// Resources
use App\Http\Resources\V1\FormatResource;

// Requests
use App\Http\Requests\Api\StoreFormatRequest;

// Modelos
use App\Models\Format;

// Facades
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formats = Format::with('entorno')->latest()->paginate();
        return FormatResource::collection($formats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormatRequest $request)
    {
        $format = Format::create($request->all());
        return response()->json(new FormatResource($format), Response::HTTP_CREATED); //201
    }

    /**
     * Display the specified resource.
     */
    public function show(Format $format)
    {
        return new FormatResource($format->load('entorno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Format $format, Request $request)
    {
        $this->authorize('update');
        $format->update($request->all());
        return response()->json(new FormatResource($format), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
