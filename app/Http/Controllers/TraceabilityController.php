<?php

namespace App\Http\Controllers;

// Modelos

use App\Models\Format;
use App\Models\Productivity;
use App\Models\Reception;

// Librerias
use Carbon\Carbon;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TraceabilityController extends Controller
{
    public function __construct()
    {
        \Carbon\Carbon::setLocale('es');
    }
    
    public function environments(Request $request)
    {
        $user = Auth::user();
        $environment_id = $user->environment_id;

        $file_number = $request->input('file_number_search');
        $format_search = $request->input('format_search');
        // $return_date = $request->input('return_date_search');

        $formats = Format::where('environment_id', $environment_id)->get();

        $files = Reception::with([
            'user', 'bases', 'user_return', 'environment_file', 'packages',
            'localidad', 'interventionType', 'productivity.user', 'status_histories.user',
        ]);

        if (!empty($file_number)) {
            session(['file_number_search' => $file_number]);
            $files->where('file_number', 'like', '%'.$file_number.'%');
        }

        if (!empty($format_search)){
            $files->where('format_id', $format_search);
        }

        $files = $files->orderBy('id', 'DESC')->paginate(50);

        $latestObservationsByFile = Productivity::latestObservationsForUser(
            $user->id,
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        $myObservationsCount = $latestObservationsByFile->count();

        $myObservationsByFormat = $latestObservationsByFile
            ->groupBy(fn ($observation) => $observation->receptions->format_id)
            ->map(fn ($group) => [
                'id' => $group->first()->receptions->format_id,
                'name' => $group->first()->receptions->bases->name ?? 'Formato',
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $myObservations = $latestObservationsByFile;

        return view('env.traceability', [
            'formats' => $formats,
            'files' => $files,
            'environment' => $user->entorno,
            'myObservationsCount' => $myObservationsCount,
            'myObservationsByFormat' => $myObservationsByFormat,
            'myObservations' => $myObservations,
        ]);
    }

    public function administrators(Request $request)
    {
        $file_number = $request->input('file_number_search');

        $files = Reception::with(['user', 'bases', 'user_return', 
            'productivity' => function($query){
                $query->withTrashed();
            },
            'productivity.user',
            'status_histories.user:id,name,last_name',
            'environment_file', 'packages']);
            
        if (!empty($file_number)) {
            session(['file_number_search' => $file_number]);
            $files->where('file_number', 'like', '%'.$file_number.'%');
        }

        $files = $files->orderBy('id', 'DESC')->limit(50)->paginate(100);

        

        return view('adm.traceability.index', [            
            'files' => $files
        ]);
    }

    public function export(Request $request)
    {
        try {
            $validateData = $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date',
                'environment_id' => 'nullable|integer',
            ]);

            if(strtotime($validateData['startDate']) > strtotime($validateData['endDate'])){
                return redirect()->back()->with('error', 'La fecha de inicio no puede ser mayor a la de fin');
            }

            $start_day = Carbon::parse($validateData['startDate'])->startOfDay();
            $end_day = Carbon::parse($validateData['endDate'])->endOfDay();

            $receptions = Reception::with([
                'user:id,name,last_name', 
                'user_return:id,name,last_name',
                'user_receive:id,name,last_name',
                'bases:id,name',
                'localidad:id,name',
                'packages:id,num_package',
                'environment_file',
                'productivity.user',
                'user_receive_env:id,name,last_name'
            ])->whereBetween('created_at', [$start_day, $end_day]);

            if ($validateData['environment_id'] != '') {
                $receptions->where('environment', $validateData['environment_id']);
            }
            
            $traceability = $receptions->get();

            if($traceability->count() < 1) {
                return redirect()->back()->with('error', 'No hay datos para la fecha seleccionada');
            }

            $header_titles = [
                'Número de ficha',
                'Formato',
                'Cantidad',
                'Fecha intervención',
                'Entorno',
                'Fecha solicitud profesional',
                'Fecha entrega a GESI',
                'Entregado por',
                'Recibido por',
                'Localidad',
                'Digitado por',
                'Usuarios/seguimientos',
                'Fecha digitación',
                'Observaciones',
                'Fecha devolución',
                'Devuelto por',
                'Recibido por (entorno)',
                'Número de paquete'
            ];

            $csv_file = new StreamedResponse(function () use ($traceability, $header_titles) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, $header_titles, ';');
                
                foreach ($traceability as $row) {
                    $typed_by = '';
                    $typed_date = '';
                    $observation = '';
                    $users_tracking = '';

                    if(!empty($row->productivity)){
                        $typed_by = $this->getUserName($row->productivity->user);
                        $typed_date = $row->productivity->created_at->format('d/m/Y');
                        $observation = !$row->productivity->observations ? '' : $row->productivity->observations;
                        $users_tracking = $row->productivity->quantity_users;
                    }

                    $user_received_by = $row->user_receive ? $this->getUserName($row->user_receive) : '';
                    $user_return = $row->user_return ? $this->getUserName($row->user_return) : '';
                    $user_return_env = $row->user_receive_env ? $this->getUserName($row->user_receive_env) : '';                    
                    $user_delivered_by = $row->user ? $this->getUserName($row->user) : '';                    

                    fputcsv($handle, [
                        $row->file_number,
                        $row->bases->name,
                        $row->quantity,
                        $row->fecha_intervencion ?? '',
                        $row->environment_file->entorno,
                        $row->required_date ? Carbon::parse($row->required_date)->format('d/m/Y') : '',
                        $row->user ? $row->created_at->format('d/m/Y') : '',
                        $user_delivered_by,
                        $user_received_by,
                        $row->localidad ? $row->localidad->name : '',
                        $typed_by,
                        $users_tracking,
                        $typed_date,
                        $observation,
                        $row->return_date ?? '',
                        $user_return,
                        $user_return_env,
                        $row->packages->num_package ?? ''
                    ], ';');
                }

                fclose($handle);
            });

            $csv_file->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $csv_file->headers->set('Content-Disposition', 'attachment; filename="Trazabilidad_fichas.csv"');

            return $csv_file;

        } catch (ValidationException  $e) {
            return redirect()->back()->with('error', 'Por favor ingrese fechas válidas');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al expotar archivo');
        }
    }

    private function getUserName($user)
    {
        if($user === null) return null;
        return properNouns(nameAndLastName($user->name, $user->last_name));
    }
}
