<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

// Modelos
use App\Models\ActasSemanales;
use App\Models\DataUser;
use App\Models\Document;
use App\Models\Entorno;
use App\Models\Format;
use App\Models\Reception;
use App\Models\User;

use Illuminate\Support\Facades\Auth;


// Services
use App\Services\PDFService;

class ActsController extends Controller
{

    protected $pdfService;

    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /**
         * Vista inicial para generar acta
         **/
        $technicians = DataUser::whereHas('user', function ($query) {
            $query->whereIn('users.role_id', [12, 7])
                ->where('users.environment_id', 0)
                ->where('users.is_active', 1);
        })->with(['user:name,last_name,id'])->get();
        
        /**
         * Ver actas generadas
         **/
        $queryDocuments = Document::whereIn('name', ['reception_acts', 'report-weekly'])
                    ->orderBy('id', 'DESC');

        if ($request->monthFilter != 'all' && $request->monthFilter != null) {
            $queryDocuments->where('document_mes', $request->monthFilter);
        }

        if ($request->environmentFilter != 'all' && $request->environmentFilter != null) {
            $queryDocuments->where('path', 'LIKE', '%/' . $request->environmentFilter . '%');
        }
        
        if ($request->typeFilter != 'all' && $request->typeFilter != null) {
            $queryDocuments->where('name', $request->typeFilter);
        }

        $documents = $queryDocuments->limit(20)->get();
        $emailTasks = ActasSemanales::with('environment')->get();

        return view('adm/acts', [
            'technicians' => $technicians,
            'documents' => $documents,
            'emailTasks' => $emailTasks
        ]);
    }

    public function environmentUsers()
    {
        $UserAuth = Auth::user();

        $environmentUsers = User::whereHas('entorno', function ($query) use ($UserAuth) {
            $query->where('is_admin', 1)
                ->where('is_active', 1)
                ->where('environment_id', '!=', 0)
                ->where('subnet_id', $UserAuth->subnet_id);
        })->get();

        return response()->json([
            'environmentUsers' => $environmentUsers
        ]);
    }


    public function getFormats(Request $request)
    {
        $initDate = $request->dates['initDate'];
        $endDate = $request->dates['endDate'];

        $formats = Reception::with('bases')->select('format_id', DB::raw('count(*) as total'))
            ->whereBetween('fecha_intervencion', [$initDate, $endDate])
            ->where('environment', $request->environmentId)
            ->groupBy('format_id')
            ->get();

        $formatsRemain = Format::whereNotIn('id', $formats->pluck('format_id'))
            ->where('environment_id', $request->environmentId)->get();

        return response()->json([
            'formats' => $formats,
            'formatsRemain' => $formatsRemain
        ]);
    }

    public function generateReceptionsAct(Request $request){
        return $this->pdfService->generateReceptionsPdf($request);
    }

    public function actsDownload(Request $request)
    {
        $year  = $request->input('yearFilter');
        $month = $request->input('monthFilter');
        $type  = $request->input('typeFilter');

        $query = Document::whereIn('name', ['reception_acts', 'report-weekly']);

        if ($year) {
            $query->where('file_year', $year);
        }

        if ($month && $month !== 'all') {
            $query->where('document_mes', $month);
        }

        if ($type && $type !== 'all') {
            $query->where('name', $type);
        }

        $documents = $query->orderBy('id', 'DESC')->get();

        if ($documents->isEmpty()) {
            return back()->with('warning', 'No se encontraron actas con los filtros seleccionados');
        }

        $zip         = new ZipArchive;
        $typeLabel   = match($type) {
            'report-weekly'  => 'Semanales',
            'reception_acts' => 'Entrega',
            default          => 'Todos',
        };
        $monthLabel  = ($month && $month !== 'all') ? '_Mes' . $month : '';
        $yearLabel   = $year ? '_' . $year : '';
        $zipFileName = "Actas_{$typeLabel}{$monthLabel}{$yearLabel}.zip";
        $zipFilePath = storage_path($zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'No se pudo crear el archivo ZIP');
        }

        foreach ($documents as $document) {
            $filePath = storage_path('app/public/' . $document->path);
            if (file_exists($filePath)) {
                $folder = $document->name === 'report-weekly' ? 'Semanales' : 'Entrega';
                $zip->addFile($filePath, $folder . '/' . basename($document->path));
            } else {
                Log::error("Acta no encontrada: $filePath");
            }
        }

        $zip->close();

        if (!file_exists($zipFilePath)) {
            Log::error("ZIP no creado: $zipFilePath");
            return back()->with('error', 'No se pudo generar el archivo ZIP');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}