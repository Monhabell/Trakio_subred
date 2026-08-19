<?php

namespace App\Http\Controllers;

// Facades

use App\Models\ActaSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

//Librerías
use ZipArchive;

// Modelos
use App\Models\DataUser;
use App\Models\GesiActivityDone;
use App\Models\Document;
use App\Models\InformePlanilla;
use App\Models\Notification;
use App\Models\Role;
use App\Models\SpecificActivity;
use App\Models\User;

class DocumentController extends Controller
{
    /**
     * Actas
     */
    public function show()
    {
        $user = Auth::user();
        $roles = null;
        $specificActivities = null;
        $doneActivities = null;
        $current_month = date('m');
        $has_insurance = Document::planillaForUserInMonth($user->id, $current_month)->exists();

        // Determinar ruta de vista
        $viewRoutes = [
            'admin_env' => '/adm/documents',
            'user_env' => '/dig/documents',
            'admin_non_env' => '/env/documents',
            'user_non_env' => '/members/documents',
        ];

        $viewRoute = $viewRoutes[
            $user->environment_id == 0 
                ? ($user->is_admin ? 'admin_env' : 'user_env')
                : ($user->is_admin ? 'admin_non_env' : 'user_non_env')
        ];

        if ($user->environment_id == 0) {
            $specificActivities = $this->getSpecificActivities($user);
            $doneActivities = $this->getDoneActivities($user);
            $roles = Role::all();
        }

        $path = storage_path('app/public/documents');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $carpetas = $this->directorioArbol($path);

        $meses = $this->getMonths();
        $users = User::where('environment_id', $user->environment_id);

        return view($viewRoute, [
            'roles' => $roles,
            'activities' => $specificActivities,
            'done_activities' => $doneActivities,
            'carpetas' => $carpetas,
            'meses' => $meses,
            'searcheUser' => $users,
            'has_insurance' => $has_insurance,
        ]);
    }

    private function getSpecificActivities($user)
    {
        return SpecificActivity::whereHas('roles', function ($query) use ($user) {
            $query->where('role_specific_activity.role_id', $user->role_id);
        })
        ->where('environment_id', $user->environment_id)
        ->orderBy('number_activity', 'ASC')
        ->get();
    }

    function getDoneActivities ($user)
    {
        return GesiActivityDone::where('role_id', $user->role_id)->get();
    }

    private function getMonths()
    {
        return [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
    }

    public function signRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usersToRequestSign' =>'array',
            'document_id' =>'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Por favor valide los datos enviados');
        }
        $users = $request->input('usersToRequestSign', []);
        $document_id = $request->input('document_id');

        if (empty($users)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un usuario.');
        }

        $auth_user = Auth::user();

        $notifications = collect($users)->map(function ($user_id) use ($auth_user, $document_id) {
            return [
                'from_user_id' => $auth_user->id,
                'to_user_id' => $user_id,
                'message' => properNouns(nameAndLastName($auth_user->name, $auth_user->last_name))." ha solicitado su firma en un documento.",
                'type' => 'document-signature',
                'status' => 0,
                'notifiable_type' => 'App\Models\Document',
                'notifiable_id' => $user_id,
                'data' => $document_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        Notification::insert($notifications);

        return redirect()->back()->with('success', "Se ha solicitado la firma de ".count($users).' usuario(s)');
    }

    public function edit(Document $document)
    {
        return view('documents/sign', [
            'document' => $document,
            'url' => $document->path
        ]);
    }

    public function update(Document $document)
    {
        $document->update([
            'updated_at' => now()
        ]);
        
        return redirect()->route('documents.index')->with('success', "¡Documento guardado!");
    }

    public function idFromPath(Request $request)
    {
        $validatedData = $request->validate([
            'document_url' => 'required|string'
        ]);

        try {
            $path = str_replace('/storage/', '', $validatedData['document_url']);
            $document_id = Document::where('path', $path)->value('id');

            if (!$document_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento no encontrado.',
                    'document_id' => null,
                ], 204);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documento encontrado.',
                'document_id' => $document_id,
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Error al obtener el ID del documento: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al procesar la solicitud.',
                'error' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Hubo un error al procesar la solicitud.',
            'error' => 'No se pudo obtener el ID del documento.',
        ], 500);
    }

    /**
     * Planillas
     */
    public function insuranceUpload(Request $request)
    {
        $user = Auth::user();

        $messages = [
            'numPlanilla.unique' => 'El número de planilla ya está registrado.',
            'certificationMonth.required' => 'El mes de certificación es obligatorio.',
            'certificationYear.required' => 'El año de certificación es obligatorio.',
            'formNumber.required' => 'El número de planilla es obligatorio.',
            'formNumber.numeric' => 'El número de formulario debe ser numérico.',
            'datePay.required' => 'La fecha de pago es obligatoria.',
            'epsValue.required' => 'El valor de EPS es obligatorio.',
            'afpValue.required' => 'El valor de AFP es obligatorio.',
            'arlValue.required' => 'El valor de ARL es obligatorio.',
            'compensationValue.numeric' => 'El valor de compensación debe ser numérico.',
            'sgssDocument.required' => 'Debe seleccionar un documento',
        ];

        $validator = Validator::make($request->all(), [
            'certificationMonth' => 'required',
            'certificationYear' => 'required',
            'formNumber' => 'required|numeric|unique:informe_planilla,numPlanilla',
            'datePay' => 'required|date',
            'epsValue' => 'required|numeric',
            'afpValue' => 'required|numeric',
            'arlValue' => 'required|numeric',
            'compensationValue' => 'nullable|numeric',
            'totalSgss' => 'required',
            'sgssDocument' => 'required|file|mimes:pdf'
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $exists = InformePlanilla::where('id_user', $user->id)
            ->where('mesCertificacion', $request->certificationMonth)
            ->where('año', $request->certificationYear)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ya se ha cargado una planilla para el mes seleccionado');
        }

        try {
            if ($request->hasFile('sgssDocument') && $request->file('sgssDocument')->isValid()) {
                $file = $request->file('sgssDocument');
                $certificationYear = $request->certificationYear;
                $certificationMonth = $request->certificationMonth;

                // Sanitizar el nombre del archivo y concatenar con una palabra adicional
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $sanitizedFileName = 'Planilla_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . $certificationYear . '/' . $certificationMonth . '/' . $user->id;

                $fullFilePath = $file->storeAs($filePath, $sanitizedFileName, 'public');

                if ($fullFilePath) {
                    InformePlanilla::create([
                        'id_user' => $user->id,
                        'mesCertificacion' => $request->certificationMonth,
                        'año' => $request->certificationYear,
                        'numPlanilla' => $request->formNumber,
                        'fechaPago' => $request->datePay,
                        'valorEps' => $request->epsValue,
                        'valorAfp' => $request->afpValue,
                        'arlValor' => $request->arlValue,
                        'valorCompensacion' => $request->compensationValue,
                        'totalPlanilla' => $request->totalSgss,
                        'cargueDoc' => $fullFilePath,
                    ]);

                    Document::create([
                        'id_user' => $user->id,
                        'name' => 'planilla',
                        'path' => $fullFilePath,
                        'file_year' => date('Y'),
                        'document_mes' => $request->certificationMonth,
                    ]);

                    return back()->with('success', 'Planilla cargada con éxito');
                }
            }
            return back()->with('error', 'No se pudo cargar el archivo.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error durante la carga del archivo: ' . $e->getMessage());
        }
    }

    public function insuranceDownload(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Definir el mapeo de números a nombres de meses
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $userLogin = Auth::user();

        $data = InformePlanilla::where('mesCertificacion', $month)
            ->where('año', $year)
            ->whereHas('usuario', function ($query) use ($userLogin) {
                $query->where('environment_id', $userLogin->environment_id);
            });

        $dataPLanillas = $data->get();

        if ($dataPLanillas->count() < 1) {
            return redirect()->back()->with('warning', 'No hay datos para la fecha seleccionada');
        }

        $response = new StreamedResponse(function () use ($dataPLanillas, $months) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, [
                'Nombre',
                'Documento',
                'Perfil',
                'Contrato',
                'No. Planilla',
                'Mes',
                'Eps',
                'Valor Eps',
                'Afp',
                'Valor Afp',
                'Arl',
                'Valor Arl',
                'Caja de Compensación',
                'Valor-Caja',
                'Valor Total'
            ], ';');

            foreach ($dataPLanillas as $row) {
                $nameUser = User::find($row->id_user);
                $perfilUser = $nameUser ? Role::find($nameUser->role_id) : null;
                $contrato = DataUser::where('id_user', $row->id_user)->first();
                $totalPlanilla1 = $row->valorEps + $row->valorAfp + $row->arlValor + $row->valorCompensacion;
                $mesNombre = isset($months[$row->mesCertificacion]) ? $months[$row->mesCertificacion] : '';

                fputcsv($handle, [
                    $nameUser ? $nameUser->name . ' ' . $nameUser->last_name : 'Desconocido',
                    $contrato->document,
                    $perfilUser ? $perfilUser->name : 'Desconocido',
                    $contrato ? $contrato->contract : 'Desconocido',
                    $row->numPlanilla,
                    $mesNombre,
                    $contrato ? $contrato->eps : 'Desconocido',
                    $row->valorEps,
                    $contrato ? $contrato->afp : 'Desconocido',
                    $row->valorAfp,
                    $contrato ? $contrato->arl : 'Desconocido',
                    $row->arlValor,
                    $contrato ? $contrato->caja : 'Desconocido',
                    $row->valorCompensacion,
                    $totalPlanilla1,
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="planillas.csv"');

        return $response;
    }

    /**
     * Prórrogas
     */
    public function prorogationUpload(Request $request)
    {
        $user = Auth::user();

        $messages = [
            'prorrogationMonth.required' => 'El mes de prorroga es obligatorio.',
            'prorrogationYear.required' => 'El año de prorroga es obligatorio.',
            'prorogrationFile.required' => 'Debe seleccionar un archivo',
            'prorogrationFile.mimes' => 'El archivo debe ser un PDF.',
            'prorogrationFile.max' => 'El tamaño máximo del archivo es de 2MB.'
        ];

        $validator = Validator::make($request->all(), [
            'prorrogationMonth' => 'required|numeric',
            'prorrogationYear' => 'required|numeric',
            'prorogrationFile' => 'required|file|mimes:pdf'
        ], $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->hasFile('prorogrationFile') && $request->file('prorogrationFile')->isValid()) {
                $certificationYear = $request->prorrogationYear;
                $certificationMonth = $request->prorrogationMonth;
                $file = $request->file('prorogrationFile');

                // Sanitizar el nombre del archivo y concatenar con una palabra adicional
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $sanitizedFileName = 'Prorroga_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . $certificationYear . '/' . $certificationMonth . '/' . $user->id;

                $fullFilePath = $file->storeAs($filePath, $sanitizedFileName, 'public');

                if ($fullFilePath) {
                    Document::create([
                        'id_user' => $user->id,
                        'name' => 'prorroga',
                        'path' => $fullFilePath,
                        'document_mes' => $request->prorrogationMonth,
                        'file_year' => $request->prorrogationYear
                    ]);

                    return back()->with('success', 'Prorroga cargada con éxito');
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * SECOP
     */
    public function secopUpload(Request $request)
    {
        $user = Auth::user();

        $messages = [
            'secopMonth.required' => 'El mes de SECOP es obligatorio.',
            'secopYear.required' => 'El año de SECOP es obligatorio.',
            'secopFile.required' => 'Debe seleccionar un archivo',
            'secopFile.mimes' => 'El archivo debe ser un PDF.',
            'secopFile.max' => 'El tamaño máximo del archivo es de 2MB.'
        ];

        $validator = Validator::make($request->all(), [
            'secopMonth' => 'required|numeric',
            'secopYear' => 'required|numeric',
            'secopFile' => 'required|file|mimes:pdf'
        ], $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->hasFile('secopFile') && $request->file('secopFile')->isValid()) {
                $certificationYear = $request->secopYear;
                $certificationMonth = $request->secopMonth;
                $file = $request->file('secopFile');

                // Nombre: {numero_contrato}_{numero_documento}_{mes+año}_SECOP.pdf
                // El SECOP se sube en la carpeta del mes en curso, pero reporta el mes anterior
                $contract = preg_replace('/[^A-Za-z0-9\-]/', '', $user->dataUser->contract ?? '');
                $documentNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $user->dataUser->document ?? '');
                $nameMonth = $certificationMonth - 1;
                if ($nameMonth < 1) {
                    $nameMonth = 12;
                }
                $monthYear = str_pad($nameMonth, 2, '0', STR_PAD_LEFT) . $certificationYear;
                $sanitizedFileName = $contract . '_' . $documentNumber . '_' . $monthYear . '_SECOP.' . $file->getClientOriginalExtension();
                $filePath = 'documents/' . $certificationYear . '/' . $certificationMonth . '/' . $user->id;

                $fullFilePath = $file->storeAs($filePath, $sanitizedFileName, 'public');

                if ($fullFilePath) {
                    Document::create([
                        'id_user' => $user->id,
                        'name' => 'secop',
                        'path' => $fullFilePath,
                        'document_mes' => $request->secopMonth,
                        'file_year' => $request->secopYear
                    ]);

                    return back()->with('success', 'SECOP cargado con éxito');
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el archivo: ' . $e->getMessage());
        }
    }

    public function prorogationDownload(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|integer',
                'month' => 'required|string|max:2',
            ]);

            $year = $request->input('year');
            $month = $request->input('month');

            $environment_id = Auth::user()->environment_id;
            $usersIds = User::where('environment_id', $environment_id)->pluck('id');

            $documentsExists = Document::with('user')->where('document_mes', $month)
                ->where('file_year', $year)
                ->where('name', 'prorroga')
                ->whereIn('id_user', $usersIds)
                ->get();                

                if (count($documentsExists) > 0) {
                    $zip = new ZipArchive;
                    $zipFileName = "Prorrogas_" . $year . "_" . $month . ".zip";
                    $zipFilePath = storage_path($zipFileName); // Correcta ruta al ZIP
                
                    // Abrir el archivo ZIP antes del bucle
                    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                        foreach ($documentsExists as $document) {
                            $userName = ucwords($document->user->name . " " . $document->user->last_name);
                            $baseDir = storage_path("app/public/documents/" . $year . "/" . $month . "/" . $document->id_user);
                           
                            if (is_dir($baseDir)) {
                                $files = glob($baseDir . '/*'); 
                                foreach ($files as $file) {
                                    $relativePath = $userName . '/' . basename($file); 
                                    if (is_file($file) && strpos(strtolower(basename($file)), 'prorroga') !== false) {
                                        $zip->addFile($file, $relativePath);
                                    }
                                }
                            } else {
                                Log::error("Directorio no encontrado: $baseDir");
                            }
                        }
                        // Cerrar el archivo ZIP después del bucle
                        $zip->close();

                        if (file_exists($zipFilePath)) {
                            return response()->download($zipFilePath)->deleteFileAfterSend(true);
                        } else {
                            Log::error("El archivo ZIP no fue creado: $zipFilePath");
                            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                        }
                
                        
                    } else {
                        return response()->json(['error' => 'No se pudo crear el archivo ZIP22222'], 500);
                    }
                }
                

            return back()->with('error', 'No se encontraron documentos con esos parámetros');
        } catch (\Throwable $th) {
            Log::error('Error al descargar el archivo: ' . $th->getMessage());
            return back()->with('error', 'Error al descargar los archivos');
        }
    }

    public function secopDownload(Request $request)
    {
        try {
            $request->validate([
                'year' => 'required|integer',
                'month' => 'required|string|max:2',
            ]);

            $year = $request->input('year');
            $month = $request->input('month');

            $environment_id = Auth::user()->environment_id;
            $usersIds = User::where('environment_id', $environment_id)->pluck('id');

            $documentsExists = Document::with('user')->where('document_mes', $month)
                ->where('file_year', $year)
                ->where('name', 'secop')
                ->whereIn('id_user', $usersIds)
                ->get();

                if (count($documentsExists) > 0) {
                    $zip = new ZipArchive;
                    $zipFileName = "Secop_" . $year . "_" . $month . ".zip";
                    $zipFilePath = storage_path($zipFileName); // Correcta ruta al ZIP

                    // Abrir el archivo ZIP antes del bucle
                    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                        foreach ($documentsExists as $document) {
                            $userName = ucwords($document->user->name . " " . $document->user->last_name);
                            $baseDir = storage_path("app/public/documents/" . $year . "/" . $month . "/" . $document->id_user);

                            if (is_dir($baseDir)) {
                                $files = glob($baseDir . '/*');
                                foreach ($files as $file) {
                                    $relativePath = $userName . '/' . basename($file);
                                    if (is_file($file) && strpos(strtolower(basename($file)), 'secop') !== false) {
                                        $zip->addFile($file, $relativePath);
                                    }
                                }
                            } else {
                                Log::error("Directorio no encontrado: $baseDir");
                            }
                        }
                        // Cerrar el archivo ZIP después del bucle
                        $zip->close();

                        if (file_exists($zipFilePath)) {
                            return response()->download($zipFilePath)->deleteFileAfterSend(true);
                        } else {
                            Log::error("El archivo ZIP no fue creado: $zipFilePath");
                            return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                        }

                    } else {
                        return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
                    }
                }

            return back()->with('error', 'No se encontraron documentos con esos parámetros');
        } catch (\Throwable $th) {
            Log::error('Error al descargar el archivo: ' . $th->getMessage());
            return back()->with('error', 'Error al descargar los archivos');
        }
    }

    public function informeDownload(Request $request)
    {
        try {
            $request->validate([
                'year'  => 'required|integer',
                'month' => 'required|string|max:2',
                'type'  => 'nullable|string|in:mensual,semanal,entrega',
            ]);

            $year  = $request->input('year');
            $month = $request->input('month');
            $type  = $request->input('type', 'mensual');

            $environment_id = Auth::user()->environment_id;
            $usersIds = User::where('environment_id', $environment_id)->pluck('id');

            $documents = Document::with('user')
                ->where('document_mes', $month)
                ->where('file_year', $year)
                ->where('name', 'report_activities')
                ->whereIn('id_user', $usersIds)
                ->get();

            if ($documents->count() === 0) {
                return back()->with('warning', 'No se encontraron informes para la fecha seleccionada');
            }

            $zip         = new ZipArchive;
            $zipFileName = "Informes_" . ucfirst($type) . "_" . $year . "_" . $month . ".zip";
            $zipFilePath = storage_path($zipFileName);

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                return back()->with('error', 'No se pudo crear el archivo ZIP');
            }

            foreach ($documents as $document) {
                $userName = ucwords($document->user->name . " " . $document->user->last_name);
                $baseDir  = storage_path("app/public/documents/{$year}/{$month}/{$document->id_user}");

                if (!is_dir($baseDir)) {
                    Log::error("Directorio no encontrado: $baseDir");
                    continue;
                }

                foreach (glob($baseDir . '/*') as $file) {
                    if (is_file($file) && stripos(basename($file), 'informe') !== false) {
                        $zip->addFile($file, $userName . '/' . basename($file));
                    }
                }
            }

            $zip->close();

            if (!file_exists($zipFilePath)) {
                Log::error("El archivo ZIP no fue creado: $zipFilePath");
                return back()->with('error', 'No se pudo generar el archivo ZIP');
            }

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } catch (\Throwable $th) {
            Log::error('Error al descargar informes: ' . $th->getMessage());
            return back()->with('error', 'Error al descargar los informes');
        }
    }

    private function addFolderToZip($zip, $folder, $basePathLength, $baseDir)
    {
        $subFolders = File::directories($folder);
        foreach ($subFolders as $subFolder) {
            $folderName = basename($subFolder);
            $user = User::find($folderName);

            // Si el usuario no existe, omitir este subfolder
            if (!$user) continue;

            $newFolderName = ucwords(strtolower($user->name . ' ' . $user->last_name));
            $relativePath = substr($subFolder, strlen($baseDir) + 1);

            $items = File::allFiles($subFolder);
            foreach ($items as $item) {
                $filePath = (string) $item;

                // Verifica si el nombre del archivo comienza con 'Prorroga'
                if (strpos(basename($filePath), 'Prorroga') === 0) {
                    // Crear una ruta única dentro del ZIP usando el nombre de la carpeta del usuario
                    $uniqueFilePath = $newFolderName . '/' . basename($filePath);
                    $zip->addFile($filePath, $uniqueFilePath);
                }
            }

            // Llamada recursiva para agregar subcarpetas adicionales
            $this->addFolderToZip($zip, $subFolder, $basePathLength, $baseDir);
        }
    }

    /**
     * Mostrar documentos
     */
    private function directorioArbol($path)
    {
        $carpetas = [];

        if (!is_dir($path)) {
            Log::error("Directorio no encontrado: $path\n");
            return $carpetas;
        }

        $archivos = scandir($path);
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                if (is_dir($path . '/' . $archivo)) {
                    $carpetas[$archivo] = $this->directorioArbol($path . '/' . $archivo);
                } else {
                    $carpetas[] = $archivo;
                }
            }
        }
        return $carpetas;
    }

    /**
     * Certificaciones
     */
    public function certifications()
    {
        return view('adm/certifications');
    }

    public function uploadCertifications(Request $request)
    {
        $year = $request->input('certificationYear');
        $month = $request->input('certificationMonth');

        $certificationsResult = [
            'errors' => [],
            'success' => [],
        ];

        $usersIdsSuccess = [];

        $request->validate([
            'certifications.*' => 'required|file|mimes:pdf',
        ]);

        if ($request->hasFile('certifications')) {
            foreach ($request->file('certifications') as $file) {
                $pdfText = $file->getClientOriginalName(); // obtener el nombre archivo
                $filename = 'Certificacion_' . $file->getClientOriginalName();

                if (preg_match('/\d{6,10}/', $pdfText, $matches)) {
                    $userDocument = $matches[0];
                    $user = DataUser::with('user')->where('document', $userDocument)->first();
                    $usersIdsSuccess[] = $user->id_user;
                    if ($user) {
                        $filePath = 'documents/' . $year . '/' . $month . '/' . $user->id_user;
                        $file->storeAs($filePath, $filename, 'public');
                        array_push($certificationsResult['success'], $user->user->name . ' ' . $user->user->last_name);
                    } else {
                        array_push($certificationsResult['errors'], $filename);
                    } 
                } else {
                    array_push($certificationsResult['errors'], $filename);
                }
            }

            return back()->with([
                'success' => count($certificationsResult['success']) . ' certificaciones cargadas con éxito. ' .
                    count($certificationsResult['errors']) . ' certificaciones no cargadas',
                'infosuccess' => $certificationsResult['success'],
                'infoerrors' => $certificationsResult['errors'],
            ]);
        }
        return back()->with('Error', 'No se cargo ni mierda');
    }

    public function destroyFromPath(Request $request)
    {
        $original_path = $request->input('ruta');
        $relative_path = str_replace('/storage', 'public', $original_path);
        $document_path = str_replace('/storage/', '', $original_path);

        $document = Document::where('path', $document_path)->first();

        if ($document) {
            $document->delete();
        }

        if (Storage::exists($relative_path)) {
            Storage::delete($relative_path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function destroy($file)
    {
        $document = Document::findOrFail($file);
        $filePath = 'public/' . $document->path;

        if (Storage::exists($filePath)) {
            if (Storage::delete($filePath)) {
                $document->delete();
                return redirect()->back()->with('success', 'Documento eliminado correctamente.');
            } else {
                return redirect()->back()->with('error', 'No se pudo eliminar el archivo.');
            }
        } else {
            return redirect()->back()->with('error', 'El archivo no existe en el almacenamiento.');
        }
    }

    public function getFolderContent(Request $request)
    {
        $path = $request->get('path', '/');
        $path = trim($path, '/');
        $fullPath = storage_path("app/public/documents/{$path}");

        // Verifica si el directorio existe
        if (!is_dir($fullPath)) {
            return response()->json(['error' => 'Ruta inválida'], 400);
        }

        $items = scandir($fullPath);
        $result = [];

        // Mapeo de números a nombres de meses
        $months = [
            '1' => 'Enero',
            '2' => 'Febrero',
            '3' => 'Marzo',
            '4' => 'Abril',
            '5' => 'Mayo',
            '6' => 'Junio',
            '7' => 'Julio',
            '8' => 'Agosto',
            '9' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];

        // Obtener el usuario autenticado
        $user = Auth::user();
        $entorno = $user->environment_id;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = "{$fullPath}/{$item}";
            $type = is_dir($itemPath) ? 'directorio' : 'archivo';
            $name = $item;
            $order = null;

            // Si es una carpeta, identificar si es mes o usuario
            if (is_dir($itemPath)) {
                // Si está dentro de un año, verificar si es un mes
                if (preg_match('/^\d{4}$/', basename($path))) { // Carpeta padre es un año
                    if (isset($months[$item])) {
                        $name = $months[$item];
                        $order = (int)$item; // Convertir número a nombre de mes
                    }
                } elseif (is_numeric($item)) { // Si no es un mes, asumir que es un usuario
                    $userFolder = User::find($item);
                    $name = $userFolder ? ucwords(strtolower($userFolder->name)) . ' ' . ucwords(strtolower($userFolder->last_name)) : "{$item}"; // Convertir ID a nombre de usuario

                    // Si el usuario es administrador, mostrar todos los usuarios en su entorno
                    if ($user->is_admin == 1) {
                        // Verifica si la carpeta pertenece al entorno del administrador
                        $userFolder = User::find($item);
                        if ($userFolder && $userFolder->environment_id != $entorno) {
                            continue; // Omitir carpetas de usuarios que no pertenecen al mismo entorno
                        }
                    } elseif ($user->is_admin == 0) {

                        $userFolder = User::find($item);

                        if ($userFolder && $userFolder->id != $user->id) {
                            continue; // Omitir carpetas de usuarios que no pertenecen al mismo entorno
                        }
                    }
                }
            }

            // Agregar el directorio o archivo al resultado
            $result[] = [
                'nombre' => $name,
                'tipo' => $type,
                'ruta' => "{$path}/{$item}",
                'original' => $item,
                'orden' => $order, // Guardar el nombre original para manejar rutas correctamente
            ];
        }

        // Ordenar por tipo (meses primero) y por número de mes si aplica
        usort($result, function ($a, $b) {
            if ($a['orden'] !== null && $b['orden'] !== null) {
                return $a['orden'] <=> $b['orden'];
            }
            return strcmp($a['nombre'], $b['nombre']);
        });

        return response()->json($result);
    }

    public function updateSign(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', 
            'originalPath' => 'required|string',         
        ]);

        $auth_user = Auth::user();
        $document_id = $request->input('id_document');

        $originalPath = $request->input('originalPath');
        $relativePath = str_replace('/storage', 'public', $originalPath);
        if (!Storage::exists($relativePath)) {
            return response()->json(['message' => 'El archivo original no existe.'], 404);
        }

        try {
            Storage::put($relativePath, file_get_contents($request->file('file')->getRealPath()));

            $update = Document::where('id', $document_id)->update([
                'signature_date' => now(),
            ]);

            // Actualizar firma para que pase al siguiente firmante
            ActaSignature::where('document_id', $document_id)
                ->where('user_id', $auth_user->id)
                ->where('is_signed', false)
                ->update([
                    'signed_at' => now(),
                    'is_signed' => true,
                ]);
            
            if (!$update) {
                return response()->json(['message' => 'Error al actualizar la fecha de firma.'], 500);
            }

            $notification = Notification::where('data', $document_id)->where('to_user_id', $auth_user->id)->first();

            if ($notification) {
                Notification::create([
                    'from_user_id' => $auth_user->id,
                    'to_user_id' => $notification->from_user_id,
                    'message' => properNouns(nameAndLastName($auth_user->name, $auth_user->last_name))." ha firmado el documento solicitado.",
                    'type' => 'document-signature-response',
                    'status' => 0,
                    'notifiable_type' => 'App\Models\Document',
                    'notifiable_id' => $auth_user->id,
                    'data' => $document_id,
                ]);

                $notification->delete();
            }
            
            return response()->json(['message' => 'Documento firmado con exito.'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al reemplazar el documento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function aprove(Document $document)
    {
        $document->update([
            'status' => 1,
        ]);

        return redirect()->back()->with('success', 'Documento aprobado.');

    }
}
