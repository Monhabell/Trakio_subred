<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Assignment;
use App\Models\DataUser;
use App\Models\Entorno;
use App\Models\Package;
use App\Models\Productivity;
use App\Models\Productivity_sds;
use App\Models\Reception;
use App\Models\ReceptionSisco;
use App\Models\ReceptionStatusHistory;
//Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PackagesController extends Controller
{
    public function index(Request $request, $requestEnvironment)
    {
        session()->forget('collapse_package_state');

        //Entorno
        $selected_environment = strtolower($requestEnvironment);
        $environment = Entorno::where('entorno', $selected_environment)->first();

        if (!$environment) {
            return redirect()->back()->withErrors(['error' => 'El entorno no existe']);
        }
        
        $environment_id = $environment->id;
        $searchPackage = $request->input('search_package');
        $searchPackageUser = $request->input('search_digitizer_id');
        $filter_by_status = $request->input('filer_by_state', []);

        // Validación de estados de paquetes
        $statusCounts = Package::where('environment_id', $environment_id)
            ->where('status', '!=', 6)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
        
        $packagesQuery = Package::with('receptions')
            ->where('environment_id', $environment_id);

        if (!empty($searchPackage)) {
            $packagesQuery->where('num_package', $searchPackage);
        }

        if(!empty($filter_by_status)){
            $packagesQuery->whereIn('status', $filter_by_status);
        }

        if(!empty($searchPackageUser)){
            $packagesQuery->whereHas('assignments', function ($query) use ($searchPackageUser) {
                $query->where('digitizer_id', $searchPackageUser);
            });
        }

        $packages = $packagesQuery
            ->withCount('receptions')
            ->orderBy('num_package', 'desc')
            ->limit(50)
            ->get();

        $packages_ids = $packages->pluck('id');

        $assignments = Assignment::with('user')
            ->whereIn('package_id', $packages_ids)
            ->get();
        
        $quantitiesDig = DB::table('receptions as r')
            ->leftJoin('productivity as p', 'p.file_id', '=', 'r.id')
            ->select(
                'r.package_id',
                DB::raw('COUNT(r.id) as count'),
                DB::raw('COUNT(p.id) as productivity_count'),
                DB::raw('COUNT(CASE WHEN p.observations IS NOT NULL THEN 1 END) as productivity_with_observations')
            )
            ->whereIn('r.package_id', $packages_ids)
            ->groupBy('r.package_id')
            ->get();        

        $data_users = DataUser::whereHas('user', function ($query) {
            $query->where('environment_id', 0);
        });

        return view('adm/packages', [
            'packages' => $packages,
            'assignments' => $assignments,
            'quantitiesDig' => $quantitiesDig,
            'environment' => $environment->entorno,
            'statusCounts' => $statusCounts,
            'data_users' => $data_users->get()
        ]);
    }

    public function return(Package $package)
    {
        $user = Auth::user();

        $package->load([
            'receptions' => function ($query) {
                $query->withCount(['productivity']);
            }
        ]);

        $is_assigned = Assignment::where('package_id', $package->id)->exists();
        $receptions = $package->receptions;
        $receptionCount = $receptions->count();
        $completeFiles = $receptions->where('productivity_count', '>', 0)->count();

        $updateFields = [
            'return_date' => now(),
            'returned_by' => $user->id,
            'status' => 5
        ];
        
        $resetFields = [
            'return_date' => null,
            'returned_by' => null
        ];

        $hasNullReturnDate = $receptions->whereNull('return_date')->isNotEmpty();
        Reception::where('package_id', $package->id)
            ->update($hasNullReturnDate ? $updateFields : $resetFields);

        // Pre-cargar en una sola consulta qué recepciones tienen productividad
        $receptionIds = $receptions->pluck('id');
        $idsConProductividad = [];
        if (!$hasNullReturnDate) {
            $idsConProductividad = Productivity::whereIn('file_id', $receptionIds)
                ->pluck('file_id')
                ->flip()
                ->all();
        }

        $changedBy = Auth::id();
        $now = now();
        $historyRows = [];

        foreach ($receptions as $reception) {
            $status = 5;

            if (!$hasNullReturnDate) {
                $status = isset($idsConProductividad[$reception->id]) ? 4 : 3;
            }

            $historyRows[] = [
                'reception_id'    => $reception->id,
                'previous_status' => $reception->status,
                'new_status'      => $status,
                'changed_by'      => $changedBy,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            Reception::where('id', $reception->id)->update(['status' => $status]);
        }

        // Insertar todos los historiales en una sola consulta
        if (!empty($historyRows)) {
            ReceptionStatusHistory::insert($historyRows);
        }

        if ($hasNullReturnDate) {
            $status = '5';
        } elseif (!is_null($package->return_digitator)) {
            $status = '4';
        } elseif ($completeFiles < $receptionCount && $completeFiles > 0) {
            $status = '2';
        } elseif ($completeFiles == $receptionCount) {
            $status = '3';
        } elseif ($completeFiles == 0 && $is_assigned) {
            $status = '1';
        } else {
            $status = '0';
        }

        if ($package->status !== $status) {
            $package->update(['status' => $status]);
        }

        return back();
    }

    public function returnDigitizer(Package $package)
    {
        $status = $package->status;
        $date_received_digitizer = null;

        if (in_array($status, ['1', '2', '3'])) {
            $status = '4';
            $date_received_digitizer = now();
        } else {
            $package->loadCount([
                'receptions',
                'receptions as reception_digited_count' => function ($query) {
                    $query->whereHas('productivity');
                }
            ]);

            $receptionTotalCount = $package->receptions_count;
            $receptionDigitedCount = $package->reception_digited_count;

            if ($receptionDigitedCount === 0) {
                $status = '1';
            } elseif ($receptionDigitedCount < $receptionTotalCount) {
                $status = '2';
            } else {
                $status = '3';
            }
        }

        if ($package->status !== $status) {
            $package->update([
                'status' => $status,
                'return_digitator' => $date_received_digitizer
            ]);
        }

        return back();
    }

    public function show(Package $package)
    {
        $package_id = $package->id;
        $productivity_observations = [];

        if ($package->type === 'sisco') {
            $files = ReceptionSisco::with([
                'format:id,name,header_time,body_time',
                'environment:id,entorno',
                'packages:id,observations,status,num_package,type',
                'deliveredBy:id,name,last_name',
            ])->where('package_id', $package_id)->get();
        }else{
            $globalDuplicates = DB::table('receptions')
                ->select('format_id', 'file_number')
                ->groupBy('format_id', 'file_number')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [ $item->format_id . '|' . $item->file_number => true ];
                });

            $files = Reception::with([
                'user:id,name,last_name',
                'bases:id,name,header_time,body_time',
                'bases.format_sds:id_sds,name_sds',
                'packages:id,observations,status,num_package,type,start_at,end_at',
                'productivity:id,dig_id,file_id,observations,type,quantity_users,type',
                'productivity.dataUser:id,id_user,url_img',
                'productivity.user:id,name,last_name',
                'users:id,name,last_name',
            ])->where('package_id', $package_id)->orderBy('order_file', 'ASC')->get();

            $loggedUserId = Auth::id();

            $calculationsByFileNumber = Productivity_sds::whereIn('file_number', $files->pluck('file_number')->unique())
                ->where('dig_id', $loggedUserId)
                ->select('file_number', 'sds_id', 'calculation_time', 'updated_at')
                ->orderByDesc('updated_at')
                ->get()
                ->groupBy('file_number');

            $files->each(function($file) use ($globalDuplicates, $calculationsByFileNumber){
                $key = $file->format_id. '|'.$file->file_number;
                $file->is_update = $globalDuplicates->has($key);

                $sdsIdsDelFormato = $file->bases?->format_sds?->pluck('id_sds')->toArray() ?? [];
                $calculation = $calculationsByFileNumber->get($file->file_number, collect())
                    ->first(fn($row) => in_array($row->sds_id, $sdsIdsDelFormato));

                $file->calculation_time = (int) ($calculation->calculation_time ?? 0);
                $file->calculation_date = $calculation->updated_at ?? null;
            });
            $productivity_observations = array_filter($files->pluck('productivity')->toArray());
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'files' => $files, 
                'productivity' => $productivity_observations,
                'current_user' => Auth::id()
            ]
        ]);
    }

    public function getTotalHours(Package $package)
    {
        $totalHours = $package->total_hours;

        return response()->json([
            'success' => true,
            'totalHours' => $totalHours
        ]);
    }
}
