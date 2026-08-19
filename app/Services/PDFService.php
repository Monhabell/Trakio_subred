<?php

namespace App\Services;

// Librerias
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

// Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// Modelos
use App\Models\Document;
use App\Models\Reception;
use App\Models\User;

class PDFService{
    public function generateReportActivities($request)
    {
        /**
         * Guardar PDF
         **/
        $year = getYear($request["init_date"]);
        $month = intval(getMonth($request["init_date"]));
        $user = Auth::user();
                
        $existInsurance = Document::where('name', 'planilla')->where('document_mes', $month)->where('id_user', $user->id)->exists();

        // if (!$existInsurance) {
        //     return redirect()->back()->with('error', "Suba la planilla SGSS para el informe de actividades de ". getTextMonthFromNumber($month));
        // }

        $directory = "documents/{$year}/{$month}/$user->id";

        $fileName = 'Informe_Actividades.pdf';
        $fileRelativePath = $directory . '/' . $fileName;

        /**
         * Registro del PDF en BD
         **/
        $fileExists = Document::where('path', $fileRelativePath)->first();

        /**
         * Almacenamiento del PDF
         **/
        Storage::disk('public')->put($fileRelativePath, '');
        $fileAbsolutePath = Storage::disk('public')->path($fileRelativePath);

        if (file_exists($fileAbsolutePath) && !$fileExists) {
            Document::create([
                'id_user' => $user->id,
                'name' => 'report_activities',
                'path' => $fileRelativePath, // Ruta relativa
                'document_mes' => intval($month),
                'file_year' => intval($year),
            ]);
        }

        $htmlContent = view('pdf/activities', [
            'environment' => $user->entorno->entorno,
            'contract_number' => $request["contract_number"],
            'init_date' => $request["init_date"],
            'end_date' => $request["end_date"],
            'person_name' => $request["person_name"],
            'document' => $request["document"],
            'role' => $request["role"],
            'text_specific_activities' => $request["text_specific_activities"],
            'text_activities_done' => $request["text_activities_done"],
            'total_fee' => $request["total_fee"],
            'total_indexacion' => $request["total_indexacion"],
            'total_fee_string' => $request["total_fee_string"],
            'fontSize' => $request["fontSize"]
        ])->render();

        //Generación del PDF con el encabezado en todas las páginas
        Browsershot::html($htmlContent)
            ->setOption('displayHeaderFooter', true)
            ->setOption('headerTemplate', $this->htmlHeaderActivities())
            ->setOption('footerTemplate', '.')
            ->margins(33, 10, 15, 7)
            ->save($fileAbsolutePath);

        // return view('pdf/activities', [
        //     'environment' => $user->entorno->entorno,
        //     'contract_number' => $request["contract_number"],
        //     'init_date' => $request["init_date"],
        //     'end_date' => $request["end_date"],
        //     'person_name' => $request["person_name"],
        //     'document' => $request["document"],
        //     'role' => $request["role"],
        //     'text_specific_activities' => $request["text_specific_activities"],
        //     'text_activities_done' => $request["text_activities_done"],
        //     'total_fee' => $request["total_fee"],
        //     'total_fee_string' => $request["total_fee_string"],
        // ]);

        return redirect()->route('documents.index')->with('success', 'Informe de actividades generado con éxito');
        // return redirect()->back()->with('success', 'Informe de actividades generado con éxito');
    }

    public function previewReportActivities(array $request)
    {
        /**
         * Guardar PDF
         **/
        $month = intval(getMonth($request["init_date"]));
        $user = Auth::user();
                
        $existInsurance = Document::where('name', 'planilla')->where('document_mes', $month)->where('id_user', $user->id)->exists();

        if (!$existInsurance) {
            return redirect()->back()->with('error', "Suba la planilla SGSS para el informe de actividades de ". getTextMonthFromNumber($month));
        }

        return view('pdf/preview-activity-file', [
            'environment' => $user->entorno->entorno,
            'contract_number' => $request["contract_number"],
            'init_date' => $request["init_date"],
            'end_date' => $request["end_date"],
            'person_name' => $request["person_name"],
            'document' => $request["document"],
            'role' => $request["role"],
            'text_specific_activities' => $request["text_specific_activities"],
            'text_activities_done' => $request["text_activities_done"],
            'total_fee' => $request["total_fee"],
            'total_fee_string' => $request["total_fee_string"],
        ]);
    }

    protected function htmlHeaderActivities()
    {
        /**
         * Imagen logo
         **/
        $imagePath = '../public/img/logoNorte.min.png';
        $imageContents = file_get_contents($imagePath);
        $base64Image = base64_encode($imageContents);

        $htmlImage = '<img src="data:image/png;base64,' . $base64Image . '" alt ="Logo norte" width = "120" style="margin-top: 10px">';

        return '
        <header style="font-family: Arial, sans-serif; margin-top: 35px; margin-left: 42px; display: flex; flex-direction: row; border: 1px solid black; max-width: 750px; box-sizing: border-box;">
            <div style="height: 60px; text-align: center; width: 150px;">
                '.$htmlImage.'
            </div>

            <div style="display: flex; flex-direction: column; border-left: 1px solid black; border-right: 1px solid black; padding: 0; width: 443px;">
                <div style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 0; height: 30px;">
                    <h1 style="margin: 0; font-size: 11px;">INFORME DE EJECUCIÓN DE CONTRATO DE PRESTACIÓN DE SERVICIOS</h1>
                </div>
                <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; width: 100%; height: 30px; padding-bottom: 0; padding-left: 0; padding-right: 0; border-top: 1px solid black;">
                    <h5 style="text-align: center; margin-top: 0; margin-bottom: 3px; font-size: 10px; font-weight: normal;">SUBRED INTEGRADA DE SERVICIOS DE SALUD NORTE E.S.E</h5>
                    <h5 style="margin: 0; font-size: 10px; font-weight: normal;">GESTIÓN CONTRACTUAL</h5>
                </div>
            </div>

            <div style="padding: 0; display: flex; flex-direction: column; width: 140px;">
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px;">
                    <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">CODIGO: AP-CT-F-50</h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                    <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">VERSIÓN: 4</h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                    <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">PAGINA <span class="pageNumber"></span> de <span class="totalPages"></span></h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 15px; border-top: 1px solid black;">
                    <h6 style="padding-left: 5px; margin: 0; font-size: 9px; font-weight: normal;">FECHA: 07/11/2024</h6>
                </div>
            </div>
        </header>';
    }

    public function generateReceptionsPdf($request)
    {
        /**
         * Entorno
         **/
        $environment = explode(',', $request->environment);
        $environmentId = $environment[0];
        $environmentName = $environment[1];

        /**
         * Rango de fechas
         **/
        $dates = [$request->initDate, $request->endDate, $request->actDate];
        $startDate = Carbon::parse($dates[0])->startOfDay();
        $endDate = Carbon::parse($dates[1])->endOfDay();

        /**
         * Datos de fichas creadas en HC
         **/
        $formatsQuantity = [];
        foreach ($request->formatsNames as $key => $format) {
            $formatsQuantity[$format] = $request->formatsQuantity[$key];
        }

        /**
         * Guardar PDF
         **/
        $separateDate = explode("-", $dates[0]);
        $directory = 'acts/receptions/' . intval($separateDate[0]) . '/' . intval($separateDate[1]);

        $fileName = $environmentName . '.pdf';
        $fileRelativePath = $directory . '/' . $fileName;

        /**
         * Registro del PDF en BD
         **/
        // $fileExists = Document::where('path', $fileRelativePath)->first();
        // if ($fileExists) {
        //     return redirect()->back()->with('error', '¡El archivo para el mes seleccionado ya existe!');
        // }

        /**
         * Almacenamiento del PDF
         **/
        Storage::disk('public')->put($fileRelativePath, '');
        $fileAbsolutePath = Storage::disk('public')->path($fileRelativePath);

        if (file_exists($fileAbsolutePath)) {
            Document::create([
                'id_user' => Auth::user()->id,
                'name' => 'reception_acts',
                'path' => $fileRelativePath, // Ruta relativa
                'document_mes' => intval($separateDate[1]),
                'file_year' => intval($separateDate[0]),
            ]);
        }

        /**
         * Datos de entrega
         **/
        $receptions = Reception::with('bases')
            ->select('format_id', DB::raw('count(*) as total'))
            ->where('environment', $environmentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('format_id')
            ->get();

        /**
         * Usuarios
         **/
        $users = $request->signatures;
        array_push($users, $request->technician);

        $usersSignatures = User::with([
            'role' => function ($query) {
                $query->select('id', 'name');
            },
            'entorno' => function ($query) {
                $query->select('id', 'entorno');
            }
        ])->whereIn('id', $users)->get();

        /**
         * Imagen logo
         **/
        $imagePath = '../public/img/logoNorte.min.png';
        $imageContents = file_get_contents($imagePath);

        $base64Image = base64_encode($imageContents);

        $htmlImage = '<img src="data:image/png;base64,' . $base64Image . '" alt ="Logo norte" width = "170" style="margin-top: 20px">';

        /**
         * Contenido HTML para el PDF
         **/
        $htmlContent = view('pdf/receptions', [
            'environment' => $environmentName,
            'usersSignatures' => $usersSignatures,
            'dates' => $dates,
            'filesQuantity' => $receptions,
            'formats' => $formatsQuantity,
            'htmlImage' => $htmlImage
        ])->render();

        /**
         * Generación de PDF
         **/

        // Generación del PDF con el encabezado en todas las páginas
        Browsershot::html($htmlContent)
            ->setOption('displayHeaderFooter', true)
            ->setOption('headerTemplate', $this->htmlHeaderReceptionActs($htmlImage))
            ->setOption('footerTemplate', '.')
            ->margins(43, 10, 20, 20)
            ->save($fileAbsolutePath);

        return redirect()->back()->with('success', 'Acta de '.strtolower($environmentName).' generada con éxito');
    }

    protected function htmlHeaderReceptionActs($htmlImage)
    {
        return '
        <header style="font-family: Arial, sans-serif; margin-top: 20px; margin-left: 75px; display: flex; flex-direction: row; border: 1px solid black; width: 650px; box-sizing: border-box;">
            <div style="height: 100px; text-align: center; width: 170px;">
                ' . $htmlImage . '
            </div>
            <div style="display: flex; flex-direction: column; border-left: 1px solid black; border-right: 1px solid black; padding: 0; width: 315px;">
                <div style="display: flex; justify-content: center; align-items: center; width: 100%; padding: 0; height: 50px;">
                    <h1 style="margin: 0; font-size: 18px;">ACTA DE REUNIÓN</h1>
                </div>
                <div style="display: flex; justify-content: center; align-items: center; flex-direction: column; width: 100%; height: 50px; padding-bottom: 0; padding-left: 0; padding-right: 0; border-top: 1px solid black;">
                    <h5 style="text-align: center; margin-bottom: 5px; font-size: 10px; font-weight: normal;">SUBRED INTEGRADA DE SERVICIOS DE SALUD NORTE E.S.E</h5>
                    <h5 style="margin: 0; font-size: 10px; font-weight: normal;">GESTION DE CALIDAD</h5>
                </div>
            </div>
            <div style="padding: 0; display: flex; flex-direction: column; width: 165px;">
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 25px;">
                    <h6 style="padding-left: 10px; margin: 0; font-size: 10px; font-weight: normal;">CODIGO: ES-GS-F-104-07</h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 25px; border-top: 1px solid black;">
                    <h6 style="padding-left: 10px; margin: 0; font-size: 10px; font-weight: normal;">VERSIÓN: 7</h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 25px; border-top: 1px solid black;">
                    <h6 style="padding-left: 10px; margin: 0; font-size: 10px; font-weight: normal;">PAGINA <span class="pageNumber"></span> de <span class="totalPages"></span></h6>
                </div>
                <div style="display: flex; justify-content: flex-start; align-items: center; height: 25px; border-top: 1px solid black;">
                    <h6 style="padding-left: 10px; margin: 0; font-size: 10px; font-weight: normal;">FECHA: 06/04/2021</h6>
                </div>
            </div>
        </header>';
    }
}