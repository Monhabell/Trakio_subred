<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ReceptionSisco;

class ReceptionSiscoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, ReceptionSisco $reception)
    {
        $validatedData = $request->validate([
            'order_file' => 'required|string|max:255',
            'format_id' => 'required|integer',
            'batch_number' => 'required|string|max:255',
        ]);

        $reception->update([
            'order_file' => $validatedData['order_file'],
            'format_id' => $validatedData['format_id'],
        ]);

        $receptionsInPackage = ReceptionSisco::select('created_at', 'id', 'order_file', 'updated_at')
            ->where('batch_number', $request->batch_number)
            ->where('id', '!=', $reception->id)
            ->orderBy('order_file', 'asc')
            ->get();

        $order = 1;

        foreach ($receptionsInPackage as $reception) {
            if ($order == $request->order_file) {
                $order++;
            }

            $reception->update(['order_file' => $order]);
            $order++;
        }

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceptionSisco $reception)
    {
        //
    }
}
