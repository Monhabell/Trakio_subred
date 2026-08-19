<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Assignment;
use App\Models\Package;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentsController extends Controller
{
    public function store(Request $request)
    {        
        $request->validate([
            'asign_digitizer_id' => 'nullable|numeric',
            'asign_package_id' => 'nullable|numeric',
            'observations_asign_package' => 'nullable|string'
        ]);

        $observations = $request->observations_asign_package;
        $user_assign_id = $request->asign_digitizer_id;
        $package_id = $request->asign_package_id;
        $messageSuccess = null;

        DB::beginTransaction();

        try {
            $package = Package::findOrFail($package_id);
            $package_status = $package->status;

            $dataUpdate = [
                'observations' => $observations
            ];

            if (!empty($user_assign_id)) {

                Assignment::create([
                    'digitizer_id' => $user_assign_id,
                    'package_id' => $package_id,
                ]);

                $messageSuccess = 'Paquete asignado correctamente';
            } 
            elseif (!empty($observations)) {

                $messageSuccess = 'Observación actualizada';
            }


            // Si estaba sin asignar
            if ($package->status == 0 && !empty($user_assign_id)) {
                $package_status = 1;
                $dataUpdate['status'] = $package_status;
                $dataUpdate['start_at'] = now();
            }

            $package->update($dataUpdate);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar el paquete: ' . $e->getMessage(), [
                'package_id' => $package_id,
                'assigned_to' => $user_assign_id,
                'observations' => $observations
            ]);
            return back()->with('error', 'Error al asignar el paquete');
        }

        return back()->with('success', $messageSuccess);
    }

    public function show()
    {
        $user_id = Auth::id();

        try {
            $packagesAssigned = Assignment::select('id', 'package_id')
            ->with([
                'package' => function ($query) {
                    $query->whereIn('status', [1,2,3])
                        ->withCount('receptions')
                        ->with([
                            'environment',
                            'receptions.bases'
                        ]);
                }
            ])
           ->where('digitizer_id', $user_id)
            ->whereHas('package', function ($query) {
                $query->whereIn('status', [1,2,3]);
            })
            ->get()
            ->each(function ($assignment) {
                if ($assignment->package) {
                    $assignment->package->total_hours =
                        $assignment->package->total_hours;
                }
            });     
        
            return response()->json(['assignments' => $packagesAssigned]);

        } catch (\Exception $e) {
            Log::error('Error al realizar la consulta de asignaciones: ' . $e->getMessage(), [
                'user_id' => $user_id
            ]);
            return response()->json(['error' => 'Error al realizar la consulta de asignaciones'], 500);
        }
    }
}