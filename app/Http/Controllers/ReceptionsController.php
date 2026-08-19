<?php
namespace App\Http\Controllers;

// Facades
use App\Http\Requests\EnvUploadCsvRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

// Modelos
use App\Models\Entorno;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Reception;
use App\Models\User;
use App\Models\ProductivitySisco;

// Requests
use App\Http\Requests\UpdateReceptionsRequest;
use App\Models\Localidades;
use App\Models\ReceptionSisco;
use App\Models\ReceptionStatusHistory;

class ReceptionsController extends Controller
{
    /**
     * Mostrar recepciones administradores
     */
    public function index(Entorno $environment)
    {
        $lastNumberPackage = $environment->load([
            'packages' => function ($query) {
                $query->select('environment_id', DB::raw('MAX(num_package) as max_package'))
                    ->groupBy('environment_id');
            }
        ])->packages->where('environment_id', $environment->id)->first()?->max_package ?? 0;

        return view('adm.receptions', [
            'environment_id' => $environment->id,
            'environment_name' => $environment->entorno,
            'lastNumberPackage' => $lastNumberPackage,
        ]);
    }

    public function list(Entorno $environment)
    {
        $environment->load(['formats' => function ($query) {
            $query->select('id', 'name', 'environment_id')->where('is_active', true);
        }]);

        $environment_id = $environment->id;

        $receptions = Reception::select('id', 'file_number', 'localidad_id', 'format_id', 'quantity', 'quantity_users', 'delivered_by', 'received_by', 'order_file', 'batch_number', 'created_at', 'fecha_intervencion')
            ->with(['user:id,name,last_name', 'bases:id,name', 'localidad:id,name'])
            ->where('environment', $environment_id)
            ->whereNotNull('delivered_by')
            ->where('status', 3)
            ->whereNull('received_by')
            ->orderBy('batch_number', 'asc')
            ->orderBy('order_file', 'asc')
            // Order by temporal
            // ->orderBy('delivered_by', 'asc')
            // ->orderBy('created_at', 'asc')
            ->limit(1000)
            ->get();

        $receptions_sisco = ReceptionSisco::select('id', 'localidad_id', 'format_id', 'subnet_id', 'consecutivo', 'delivered_by', 'order_file', 'batch_number', 'created_at', 'intervention_date', 'consecutivo_sisco')
            ->with(['format:id,name', 'localidad:id,name,identificador', 'users:id,name,last_name', 'subnet:id,name', 'deliveredBy:id,name,last_name'])
            ->where('environment_id', $environment_id)
            ->where('status', 3)
            ->orderBy('batch_number', 'asc')
            ->orderBy('order_file', 'asc')
            ->limit(1000)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'environment' => $environment,
                'receptions' => $receptions,
                'receptions_sisco' => $receptions_sisco,
                'receptions_quantity' => $receptions->count()
            ]
        ]);
    }

    public function receptionsDashboard()
    {
        $countReceptions = Reception::where('receptions.received_by', '!=', null)
            ->where('receptions.return_date', null)
            ->leftJoin('productivity', 'productivity.file_id', '=', 'receptions.id')
            ->select(
                DB::raw('receptions.environment AS environment'),
                DB::raw('COUNT(receptions.id) AS total_count'),
                DB::raw('COUNT(productivity.id) AS digitadas')
            )
            ->groupBy('receptions.environment')
            ->get();

        $environments = Entorno::whereNotIn('id', [0, 1])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'receptions' => $countReceptions,
                'environments' => $environments
            ]
        ]);
    }

    /**
     * Agregar ficha a paquete
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'newFile' => 'required|string|max:255',
                'newFormat' => 'required|integer',
                'newQuantity' => 'required|integer',
                'newDeliveredBy' => 'required|string|max:255',
                'newEnvironmentId' => 'required|integer',
                'newPackageId' => 'required|integer',
                'newBatchNumber' => 'required|integer',
                'newFechaIntervencion' => 'required|date',
                'newOrder' => 'required|integer',
            ]);

            $fileExists = Reception::where('file_number', $request->input('newFile'))
                ->where('file_number', $request->input('newFile'))
                ->whereIn('status', [5, 6, 14, 7, 15]) // Solo en casos de actualización
                ->first();

            if ($validator->fails()) {
                //return response()->json(['errors' => $validator->errors()], 422);
                return back()->with(['errors' => 'Error en los campos']);
            }

            $receptionCreated = Reception::create([
                'environment' => $request->input('newEnvironmentId'),
                'file_number' => $request->input('newFile'),
                'consecutivo' => substr($request->input('newFile'), 5, strlen($request->input('newFile'))),
                'fecha_intervencion' => $request->input('newFechaIntervencion'),
                'format_id' => $request->input('newFormat'),
                'quantity' => $request->input('newQuantity') ? $request->input('newQuantity') : 1,
                'delivered_by' => $request->input('newDeliveredBy'),
                'received_by' => Auth::id(),
                'package_id' => $request->input('newPackageId'),
                'batch_number' => $request->input('newBatchNumber'),
                'order_file' => $request->input('newOrder'),
            ]);

            // Reordenar fichas en el paquete según el campo order_file
            $receptionsInPackage = Reception::where('package_id', $request->input('newPackageId'))
                ->orderBy('order_file', 'asc')
                ->get();

            $order = 1;
            foreach ($receptionsInPackage as $reception) {
                $reception->update(['order_file' => $order]);
                $order++;
            }

            // Asociar profesional si se proporciona
            $professionalId = $request->input('newProfessional');
            if ($professionalId) {
                $receptionCreated->users()->attach($professionalId);
            }

            return back()->with(['success' => 'Ficha creada correctamente']);
        } catch (\Exception $e) {
            return back()->with(['errors' => 'Error al guardar la ficha']);
        }
    }

    /**
     * Edición fichas
     */
    public function update(Request $request, Reception $reception)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_number' => 'nullable|string|max:15',
                'format_id' => 'nullable|integer',
                'quantity_users' => 'nullable|integer',
                'order_file' => 'nullable|integer',
                'batch_number' => 'nullable|integer',
                'fecha_intervencion' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $reception->update([
                'file_number' => $request->file_number ?? $reception->file_number,
                'quantity_users' => $request->quantity_users ?? $reception->quantity_users,
                'format_id' => $request->format_id ?? $reception->format_id,
                'quantity' => $request->quantity ?? $reception->quantity,
                'order_file' => $request->order_file ?? $reception->order_file,
                'fecha_intervencion' => $request->fecha_intervencion ?? $reception->fecha_intervencion
            ]);

            $whereColumn = isset($request->package_id) ? 'package_id' : 'batch_number';
            $value = isset($request->package_id) ? $request->package_id : $request->batch_number;
    
            $receptionsInPackage = Reception::select('created_at', 'id', 'order_file', 'file_number', 'updated_at')
                ->where($whereColumn, $value)
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
            
            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado'
            ]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar la ficha: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el registro'
            ], 500);
        }
    }

    /**
     * Recepción administradores
     */
    public function receive(UpdateReceptionsRequest $request)
    {
        $idFiles = $request->input('receptionsFiles', []);

        if (empty($idFiles)) {
            return redirect()->back()->with('error', 'Por favor seleccione al menos una ficha para recibir');
        }

        $count_files = count($idFiles);
        $package = null;
        $success_message = '';
        $package_number = $request->environment_id . $request->num_package;

        // Consultar si el numero de paquete ya existe
        $package_exists = Package::where('num_package', $package_number)
            ->where('environment_id', $request->environment_id)
            ->first();

        $batchNumber = null;

        // Si el paquete no existe, crear uno nuevo
        if (!$package_exists) {
            $package = $this->createNewPackage($package_number, $request->environment_id);

            if (!$package) {
                return redirect()->back()->with('error', 'Error al crear el paquete');
            }

            $success_message = "$count_files ficha(s) recibida(s), con número de paquete $package_number";
        } else {
            $updatePackage = $this->processPackageExisting($package_exists);
            $batchNumber = Reception::where('package_id', $package_exists->id)->max('batch_number');
            if ($updatePackage === false) {
                return redirect()->back()->with('error', 'Error al actualizar el paquete');
            }

            if ($updatePackage === null) {
                return redirect()->back()->with('error', 'El paquete ya fue devuelto o está en proceso de devolución')->send();
            }

            $success_message = "Se añadieron $count_files fichas al paquete $package_number";
        }

        $package_id = $package_exists ? $package_exists->id : ($package->id ?? null);
        $newStatusFiles = 11; // Recibido por GESI

        if(!$batchNumber){
            Reception::whereIn('id', $idFiles)
                ->update([
                    'received_by' => Auth::id(),
                    'package_id' => $package_id,
                    'status' => $newStatusFiles,
                ]);
        }else{
             Reception::whereIn('id', $idFiles)
                ->update([
                    'received_by' => Auth::id(),
                    'package_id' => $package_id,
                    'status' => $newStatusFiles,
                    'batch_number' => $batchNumber
                ]);
        }

        $receptions = Reception::whereIn('id', $idFiles)->get();

        foreach ($receptions as $reception) {
            ReceptionStatusHistory::create([
                'reception_id' => $reception->id,
                'previous_status' => $reception->status,
                'new_status' => $newStatusFiles,
                'changed_by' => Auth::id()
            ]);
        }

        return redirect()->back()->with('success', $success_message);
    }

    private function createNewPackage($packageNumber, $environmentId, $type = 'basic')
    {
        $package = Package::create([
            'num_package' => $packageNumber,
            'status' => '0',
            'environment_id' => $environmentId,
            'type' => $type
        ]);

        return $package ?? null;
    }

    private function processPackageExisting($package)
    {
        if (!$package)
            return false;

        if ($package->status === "6" || $package->status === "5") {
            return null;
        }

        if ($package->status === "3" || $package->status === "4") {
            if (!$package->update(['status' => '2'])) {
                return false;
            }
        }

        return true;
    }

    public function receiveSisco(Request $request)
    {
        $idFiles = $request->input('receptionsFilesSisco', []);

        if (empty($idFiles)) {
            return redirect()->back()->with('error', 'Por favor seleccione al menos una ficha para recibir');
        }

        $count_files = count($idFiles);
        $package = null;
        $success_message = '';
        $package_number = $request->environment_id . $request->num_package;

        // Consultar si el numero de paquete ya existe
        $package_exists = Package::where('num_package', $package_number)
            ->where('environment_id', $request->environment_id)
            ->first();

        $batchNumber = null;

        // Si el paquete no existe, crear uno nuevo
        if (!$package_exists) {
            $package = $this->createNewPackage($package_number, $request->environment_id, 'sisco');

            if (!$package) {
                return redirect()->back()->with('error', 'Error al crear el paquete');
            }

            $success_message = "$count_files ficha(s) recibida(s), con número de paquete $package_number";
        } else {
            $updatePackage = $this->processPackageExisting($package_exists);
            $batchNumber = ReceptionSisco::where('package_id', $package_exists->id)->max('batch_number');
            if ($updatePackage === false) {
                return redirect()->back()->with('error', 'Error al actualizar el paquete');
            }

            if ($updatePackage === null) {
                return redirect()->back()->with('error', 'El paquete ya fue devuelto o está en proceso de devolución')->send();
            }

            $success_message = "Se añadieron $count_files fichas al paquete $package_number";
        }

        $package_id = $package_exists ? $package_exists->id : ($package->id ?? null);
        $newStatusFiles = 11; // Recibido por GESI

        ReceptionSisco::whereIn('id', $idFiles)
            ->update([
                'package_id' => $package_id,
                'status' => $newStatusFiles,
                'batch_number' => $batchNumber ?? $request->input('batch_number', null)
            ]);

        $receptions = ReceptionSisco::whereIn('id', $idFiles)->get();

        foreach ($receptions as $reception) {
            ReceptionStatusHistory::create([
                'reception_id' => $reception->id,
                'previous_status' => $reception->status,
                'new_status' => $newStatusFiles,
                'changed_by' => Auth::id()
            ]);
        }

        return redirect()->back()->with('success', $success_message);
    }

    private function createSisco($environment_id, $packageId, $quantity, $createSisco, $formatId)
    {
        for ($i = 0; $i < $quantity; $i++) {
            $createSisco[] = [
                'environment_id' => $environment_id,
                'package_id' => $packageId,
                'format_id' => $formatId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        ProductivitySisco::insert($createSisco);
    }

    /**
     * Recepción entornos
     */
    public function envReceptionShow()
    {
        $user = Auth::user();

        $receptions = Reception::with([
            'user_return:id,name,last_name',
            'bases:id,name',
            'localidad:id,name',
            'users:id,name,last_name',
            'productivity:id,file_id,observations',
            'packages:id,num_package',
        ])
        ->where('delivered_by', $user->id)
        ->whereNull('received_by_environment')
        ->whereNotNull('return_date')
        ->orderBy('package_id', 'asc')
        ->orderBy('order_file', 'asc')
        ->get();

        // Agrupamos directamente. Eloquent mantiene los objetos dentro del grupo.
        $packages = $receptions->groupBy('package_id');

        return view('env.reception', [
            'packages' => $packages,
            'receptions_quantity' => $receptions->count(),
        ]);
    }

    public function envDevProfShow()
    {
        $user = Auth::user();

        $returns = Reception::with([
                'bases:id,name', 
                'packages:id,num_package', 
                'users:id,name,last_name'
            ])
            ->whereNotNull('received_by_environment')
            ->whereIn('status', [14, 15]) // corregido: whereIn para array
            ->where('received_by_environment', $user->id)
            ->orderBy('package_id', 'asc')
            ->get()
            ->groupBy('package_id')
            ->map(function ($group) {
                return $group->sortBy('order_file')->values();
            });

        return view('env.devprof', [
            'packages' => $returns,
            'count' => $returns->flatten()->count()
        ]);
    }


    public function envReturnProcess(Request $request)
    {
        $request->validate([
            'selected_files' => 'required|array',
        ]);

        $user = Auth::user();
        $receptions = Reception::whereIn('id', $request->selected_files)->get();
        $status = 6; // entregado a profesional
        
        $receptionsUpdate = Reception::whereIn('id', $request->selected_files)
            ->update([
                'status' => $status,
            ]);

        if ($receptionsUpdate) {
            
            foreach ($receptions as $reception) {
                ReceptionStatusHistory::create([
                    'reception_id' => $reception->id,
                    'previous_status' => $reception->status,
                    'new_status' => $status,
                    'changed_by' => $user->id
                ]);
            }
        }

        // succes de la cantidfad entregadas al profesional

        return redirect()->back()->with('success', count($request->selected_files) . ' ficha(s) devuelta(s) para profesional ' . $request->receiver_name);
    }



    public function envReceive(Request $request)
    {
        $checkboxes = $request->input('receptionsFiles', []);
        if (!empty($checkboxes)) {
            $user = Auth::user();
            $receptions = Reception::whereIn('id', $checkboxes)->get();
            $status = 14; // Recibido por entorno

            $updateReception = Reception::whereIn('id', $checkboxes)
                ->update([
                    'received_by_environment' => $user->id,
                    'status' => $status // Recibido por entorno
                ]);

            if ($updateReception) {
                foreach ($receptions as $reception) {
                    ReceptionStatusHistory::create([
                        'reception_id' => $reception->id,
                        'previous_status' => $reception->status,
                        'new_status' => $status, // Recibido por entorno
                        'changed_by' => $user->id
                    ]);
                }
            }

                
            // Registrar historial de estado antes de la actualización


            $current_package = null;

            // Actualización de estado de paquetes
            foreach ($checkboxes as $id_file) {
                $file_package_id = Reception::where('id', $id_file)->value('package_id');

                if ($file_package_id != $current_package) {
                    Package::where('id', $file_package_id)->update([
                        'status' => '6'
                    ]);
                }

                $current_package = $file_package_id;
            }

            return redirect()->back()->with('success', 'Se recibieron ' . count($checkboxes) . ' ficha(s) correctamente');
        } else {
            return redirect()->back()->with('error', 'Por favor seleccione al menos una ficha para recibir');
        }
    }

    public function environmentDeliver()
    {
        $receptions = Reception::with([
            'localidad:id,name',
            'bases:id,name',
        ])
            ->whereNotNull('delivered_by')
            ->where('status', 3)
            ->where('delivered_by', Auth::id())
            ->whereNull('received_by')
            ->orderBy('batch_number', 'ASC')
            ->orderBy('order_file', 'ASC');

        $receptions_quantity = $receptions->count();
        $receptions = $receptions->limit(500)->get();

        return view('env.deliver', [
            'receptions' => $receptions,
            'receptions_quantity' => $receptions_quantity
        ]);
    }

    public function envUploadCsvFiles(EnvUploadCsvRequest $request)
    {
        $userId = Auth::id();
        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $data = array_map('str_getcsv', file($filePath));
        $receptions = [];
        $maxBatchNumber = Reception::max('batch_number');

        try {
            DB::beginTransaction();

            foreach (array_slice($data, 1) as $key => $row) {
                $data_columns = explode(';', $row[0]);
                $localidadId = Localidades::where('identificador', substr($data_columns[0], 1, 2))->value('id');
                $receptions[] = [
                    'environment' => $data_columns[4],
                    'localidad_id' => $localidadId,
                    'file_number' => $data_columns[0],
                    'consecutivo' => substr($data_columns[0], 5, strlen($data_columns[0])),
                    'format_id' => $data_columns[1],
                    'quantity' => $data_columns[2],
                    'order_file' => $key + 1,
                    'batch_number' => $maxBatchNumber + 1,
                    'delivered_by' => $userId,
                    'status' => 3,
                    'fecha_intervencion' => $data_columns[3],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            Reception::insert($receptions);

            DB::commit();
            $this->createNotification(count($receptions));

            return redirect()->back()->with('success', 'Fichas cargadas con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cargar el archivo: ' . $e->getMessage());
        }
    }

    private function createNotification($receptionCount)
    {
        $user = Auth::user();
        $environmentName = Entorno::find($user->environment_id)->entorno;
        $techniciansId = User::whereIn('role_id', [7, 10, 12])->where('environment_id', 0)->pluck('id');
        $notifications = [];

        foreach ($techniciansId as $technicianId) {
            $notifications[] = [
                'from_user_id' => $user->id,
                'to_user_id' => $technicianId,
                'message' => "{$environmentName} ha cargado {$receptionCount} fichas(s)",
                'type' => 'reception',
                'status' => 0,
                'notifiable_type' => Reception::class,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'data' => $environmentName
            ];
        }

        if (!empty($notifications)) {
            Notification::insert($notifications);
        }
    }

    /**
     * Eliminar fichas
     */
    public function destroy(Request $request)
    {
        $this->authorize('destroy', Reception::class);

        $message = '';
        $receptionIds = (array) $request->input('id');

        if (empty($receptionIds)) {
            $message = 'No se proporcionaron fichas para eliminar.';
            Session::flash('error', $message);
            return response()->json(['message' => $message], 400);
        }

        DB::beginTransaction();

        try {
            $receptions = Reception::whereIn('id', $receptionIds)->get();

            // Si no se encuentran recepciones
            if ($receptions->isEmpty()) {
                $message = 'No se encontraron fichas con los IDs proporcionados.';
                DB::rollBack();
                Session::flash('error', $message);
                return response()->json(['message' => $message], 404);
            }

            // Eliminar las recepciones
            $result = Reception::whereIn('id', $receptionIds)->delete();

            if ($result) {
                $message = count($receptions) > 1
                    ? count($receptionIds) . " fichas eliminadas correctamente"
                    : "Ficha {$receptions->first()->file_number} eliminada";

                DB::commit();
                Session::flash('success', $message);
                return response()->json(['message' => $message]);
            }

            $message = count($receptions) > 1
                ? 'No se pudieron eliminar las fichas'
                : "No se pudo eliminar la ficha {$receptions->first()->file_number}";

            DB::rollBack();
            Session::flash('error', $message);
            return response()->json(['message' => $message], 500);

        } catch (\Throwable $th) {
            $message = 'Error al eliminar las fichas.';
            DB::rollBack();
            Session::flash('error', $message);
            return response()->json(['message' => $message], 500);
        }
    }

    public function returnToFix(Request $request)
    {
        $ids = $request->input('ids', []);
        $type = $request->input('type', 'normal');
        $message = '';

        if (empty($ids)) {
            $message = 'No se proporcionaron fichas para devolver.';
            Session::flash('error', $message);
            return response()->json(['message' => $message], 400);
        }

        $model = $type === 'sisco' ? ReceptionSisco::class : Reception::class;
        $historyKey = $type === 'sisco' ? 'reception_sisco_id' : 'reception_id';

        DB::beginTransaction();

        try {
            $receptions = $model::whereIn('id', $ids)->get();

            if ($receptions->isEmpty()) {
                DB::rollBack();
                $message = 'No se encontraron fichas con los IDs proporcionados.';
                Session::flash('error', $message);
                return response()->json(['message' => $message], 404);
            }

            $newStatus = 15; // Corrección punteo

            $updpateStatus = $model::whereIn('id', $ids)
                ->update([
                    'status' => $newStatus,
                    'delivered_by' => null,
                    'order_file' => null,
                    'batch_number' => null
                ]);

            if ($updpateStatus) {
                foreach ($receptions as $reception) {
                    ReceptionStatusHistory::create([
                        $historyKey => $reception->id,
                        'previous_status' => $reception->status,
                        'new_status' => $newStatus,
                        'changed_by' => Auth::id()
                    ]);
                }
            }

            DB::commit();
            $message = count($ids) > 1
                ? count($ids) . ' fichas devueltas para corrección.'
                : 'Ficha devuelta para corrección.';
            Session::flash('success', $message);
            return response()->json(['message' => $message]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Error al devolver las fichas.'], 500);
        }
    }
}
