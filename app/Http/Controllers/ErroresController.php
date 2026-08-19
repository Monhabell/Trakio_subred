<?php

namespace App\Http\Controllers;

use App\Models\Entorno;
use App\Models\ErrorRecord;
use App\Models\ErrorRecordField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErroresController extends Controller
{
    public function show(Request $request)
    {
        $entornoName = $request->entorno;
        $environment = Entorno::where('entorno', $entornoName)->first();

        $fichas = collect();

        if ($environment) {
            $fichas = ErrorRecord::with(['pendingFields.field', 'errorImport.format'])
                ->withCount('recordFields as total_fields_count')
                ->where('environment_id', $environment->id)
                ->where('user_id', Auth::id())
                ->get()
                ->filter(fn ($record) => $record->pendingFields->isNotEmpty())
                ->values();
        }

        return view('dig.errors.my-errors', [
            'entorno' => $entornoName,
            'fichas' => $fichas,
        ]);
    }

    public function resolveField(Request $request, ErrorRecordField $errorRecordField)
    {
        $request->validate([
            'resolved' => 'required|boolean',
        ]);

        try {
            $errorRecordField->update([
                'resolved_at' => $request->boolean('resolved') ? now() : null,
                'resolved_by' => $request->boolean('resolved') ? Auth::id() : null,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
