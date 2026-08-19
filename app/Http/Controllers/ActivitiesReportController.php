<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Modelos
use App\Models\Document;
use App\Models\Role;
use App\Models\SpecificActivity;
use App\Models\UserHour;
use App\Models\TemplateReportActivity;
use App\Models\TextActivityTemplate;

// Request
use App\Http\Requests\GenerateActivitiesRequest;
use App\Http\Requests\StoreActivitiesRequest;

// Services
use App\Services\PDFService;

class ActivitiesReportController extends Controller
{
    protected $pdfService;

    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function save(StoreActivitiesRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($request->role_id);
            $role->update([
                'hour_value' => $request->hour_value,
                'total_fee' => $request->total_salary
            ]);

            if ($request->has('specific_activity') && $request->has('activityId')) {
                $this->updateSpecificActivities($request->role_id, $request->specific_activity, $request->activityId);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Información actualizada')->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo actualizar la información, por favor intente nuevamente')->withInput();
        }
    }

    private function updateSpecificActivities($role_id, $activities, $activitiesIds)
    {
        $existingActivities = SpecificActivity::whereIn('id', $activitiesIds)->get()->keyBy('id');

        foreach ($activities as $key => $activity) {
            if (!empty($activity)) {
                $specificActivity = null;

                if (!empty($activitiesIds[$key]) && $existingActivities->has($activitiesIds[$key])) {
                    $specificActivity = $existingActivities[$activitiesIds[$key]];
                    $specificActivity->update([
                        'description' => $activity,
                        'number_activity' => $key + 1,
                    ]);
                } else {
                    $specificActivity = SpecificActivity::create([
                        'description' => $activity,
                        'number_activity' => $key + 1,
                        'environment_id' => Auth::user()->environment_id
                    ]);
                }
                $specificActivity->roles()->syncWithoutDetaching($role_id);
            }
        }
    }

    public function reportActitivitesShow(Request $request)
    {
        $user = Auth::user();
        $current_month = date('m');
        $current_year = date('Y');

        $has_insurance = Document::planillaForUserInMonth($user->id, $current_month)->exists();

        $loaded_template = [];

        if ($request->template) {
            $loaded_template = TextActivityTemplate::with('template')->where('template_id', $request->template)->get();
        }

        $overtTimes = UserHour::where('user_id', $user->id)
            ->where('year', $current_year)
            ->where('number_month', $current_month)
            ->get();

        $specificActivities = SpecificActivity::whereHas('roles', function ($query) use ($user) {
            $query->where('role_specific_activity.role_id', $user->role_id);
        })
            ->where('environment_id', $user->environment_id)
            ->orderBy('number_activity', 'ASC')->get();

        $templates = TemplateReportActivity::where('user_id', $user->id)->get();

        return view('documents/report-activities', [
            'activities' => $specificActivities,
            'total_overtime' => $overtTimes,
            'templates' => $templates,
            'loaded_template' => $loaded_template,
            'edit_template' => false,
            'has_insurance' => $has_insurance,
        ]);
    }

    public function reportActitivitesEditForm($template)
    {
        $loaded_template = TextActivityTemplate::with('template')->where('template_id', $template)->get();

        $user = Auth::user();
        $current_month = date('m');
        $current_year = date('Y');

        $has_insurance = Document::planillaForUserInMonth($user->id, $current_month)->exists();

        $overtTimes = UserHour::where('user_id', $user->id)
            ->where('year', $current_year)
            ->where('number_month', $current_month)
            ->get();

        $specificActivities = SpecificActivity::whereHas('roles', function ($query) use ($user) {
            $query->where('role_specific_activity.role_id', $user->role_id);
        })
            ->where('environment_id', $user->environment_id)
            ->orderBy('number_activity', 'ASC')->get();

        // $templates = TemplateReportActivity::where('user_id', $user->id)->get();

        return view('documents/report-activities', [
            'activities' => $specificActivities,
            'total_overtime' => $overtTimes,
            'templates' => null,
            'loaded_template' => $loaded_template,
            'edit_template' => true,
            'has_insurance' => $has_insurance
        ]);
    }

    public function actionsReportActivities(Request $request)
    {
        if ($request->type_action == 'generate-pdf') {
            $validatedData = app(GenerateActivitiesRequest::class)->validated();
            if (Auth::user()->environment_id == 0 ){
                
                return $this->pdfService->generateReportActivities($request);
            }
            
            return $this->pdfService->previewReportActivities($validatedData);
        } else if ($request->type_action == 'save-template') {
            return $this->generateReportActivitiesTemplate($request);
        } else if ($request->type_action == 'update-template') {
            return $this->updateTemplate($request);
        }
    }

    public function reportActivitiesGenerate (Request $request) {
        return $this->pdfService->generateReportActivities($request);
    }

    protected function updateTemplate($request)
    {
        try {
            $template = TextActivityTemplate::where('template_id', $request->template_id)
                ->get()
                ->keyBy('position');

            if ($template->isEmpty()) {
                return redirect()->back()->with('error', 'Plantilla no encontrada');
            }

            $cases = '';
            $ids = [];

            foreach ($request->text_activities_done as $key => $textActivity) {
                $position = $key + 1;

                if (isset($template[$position])) {
                    $id = $template[$position]->id;
                    $cases .= "WHEN id = $id THEN '$textActivity' ";
                    $ids[] = $id;
                }
            }

            if (!empty($cases)) {
                $ids = implode(',', $ids);
                DB::statement("
                UPDATE text_activities_templates
                SET description = CASE $cases END
                WHERE id IN ($ids)
            ");
            }

            return redirect()->route('report.activities.show', ['template' => $request->template_id])->with('success', 'Plantilla actualizada');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'No se pudo actualizar la plantilla');
        }
    }

    public function dropTemplate($template)
    {
        DB::beginTransaction();

        try {
            TextActivityTemplate::where('template_id', $template)->delete();
            TemplateReportActivity::where('id', $template)->delete();

            DB::commit();

            return redirect()->route('report.activities.show')->with('success', 'Plantilla eliminada exitosamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return redirect()->route('report.activities.show')->with('error', 'No se pudo eliminar la plantilla');
        }
    }

    protected function generateReportActivitiesTemplate($request)
    {
        if (empty($request->template_name)) {
            return redirect()->back()->with('error', 'El nombre de la plantilla es obligatorio');
        }

        $user_id = Auth::id();

        $templateCount = TemplateReportActivity::where('user_id', $user_id)->count();

        if ($templateCount >= 3) {
            return redirect()->back()->with('error', 'Has alcanzado el número máximo de plantillas permitidas (3)');
        }

        DB::beginTransaction();

        try {
            $template = TemplateReportActivity::create([
                'name' => $request->template_name,
                'user_id' => $user_id,
            ]);

            $activities = [];
            foreach ($request->text_activities_done as $key => $activity) {
                $activities[] = [
                    'description' => $activity,
                    'template_id' => $template->id,
                    'position' => $key + 1,
                ];
            }

            TextActivityTemplate::insert($activities);

            DB::commit();

            return redirect()->back()->with('success', 'Plantilla guardada exitosamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error al generar la plantilla: ' . $th->getMessage());

            return redirect()->back()->with('error', 'Ha ocurrido un error al guardar la plantilla');
        }
    }

    public function delete(SpecificActivity $activity)
    {
        try {
            $result = $activity->delete();
            $message = $result ? 'Actividad eliminada correctamente' : 'No se pudo eliminar la actividad.';

            return response()->json([
                'success' => true,
                'message' => $message
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar la actividad.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
