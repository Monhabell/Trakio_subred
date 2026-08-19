<?php
namespace App\Http\Controllers;

// Modelos
use App\Models\Format;

// Request
use App\Http\Requests\UpdateFormatRequest;
use App\Models\AdicionalFormat;
use App\Models\FormatSds;
use App\Models\Reception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// Facades
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FormatController extends Controller
{
    public function index()
    {
        $formatosPadresIds = Format::whereNotIn('id', function ($query) {
            $query->select('id_format_adicional')->from('adicional_format');
        })
            ->where('is_active', 1)
            ->pluck('id');

        $formatosLibresIds = Format::whereNotIn('id', function ($query) {
            $query->select('id_format')
                ->from('adicional_format')
                ->union(
                    DB::table('adicional_format')->select('id_format_adicional')
                );
        })
        ->where('is_active', 1)
        ->pluck('id');

        $bases = Format::with(['entorno', 'format_sds', 'adicionales'])->whereNot('environment_id', 1)
            ->orderBy('environment_id', 'ASC')->get();

        // Agregar en la consulta $bases si el formato está en la consulta formatos
        $bases->each(function ($base) use ($formatosPadresIds, $formatosLibresIds) {
            $base->is_dad  = $formatosPadresIds->contains($base->id);
            $base->is_free = $formatosLibresIds->contains($base->id);
        });

        return view('adm.formats.index', ['bases' => $bases]);
    }

    public function show(Format $format)
    {
        $format->load(['entorno', 'format_sds']);
        $formatsSds = FormatSds::where('environment_id', $format->environment_id)->get();
        return response()->json(['format' => $format, 'formatsSds' => $formatsSds], Response::HTTP_OK);
    }

    public function create (Format $format)
    {
        $formatsSds = FormatSds::where('environment_id', $format->environment_id)->get();
        return view('adm.formats.create', ['format' => $format, 'formatsSds' => $formatsSds]);
    }

    public function store(UpdateFormatRequest $request)
    {
        try {
            DB::beginTransaction();
            $formatCreated = Format::create($request->validated());
            if ($formatCreated) {
                $formatCreated->format_sds()->attach($request->input('format_id_sds'));
            } else {
                throw new \Exception('No se pudo crear la base');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Base agregada exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al agregar la base')->withInput();
        }
    }

    public function edit(Format $format)
    {
        $format->load(['format_sds']);
        $formatsSds = FormatSds::where('environment_id', $format->environment_id)->get();
        return view('adm.formats.edit', ['format' => $format, 'formatsSds' => $formatsSds]);
    }

    public function update(UpdateFormatRequest $request, Format $format)
    {
        try{
            $update = $format->update($request->validated());
            if(!$update){
                throw new \Exception('No se pudo actualizar la base');
            }
            $format->format_sds()->sync($request->input('format_id_sds'));
            return redirect()->back()->with('success', 'Base actualizada correctamente');
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar la base');
        }
    }

    public function getSiscoByEnvironment($id)
    {
        $formats = Format::where('environment_id', $id)
            ->where('name', 'like', '%sisco%')
            ->get();

        return response()->json($formats);
    }

    public function environmentBases($environment)
    {        
        $bases = Format::where('environment_id', $environment)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();
        return response()->json(['bases' => $bases]);
    }

    public function formatsCountGesi()
    {
        $user = Auth::user();
        $noReturned = Reception::whereNull('return_date')->where('environment', $user->environment_id)
            ->leftJoin('formats', 'receptions.format_id', '=', 'formats.id')
            ->select(
                DB::raw('formats.name AS format'),
                DB::raw('COUNT(receptions.id) AS total_count'),
            )
            ->groupBy('formats.name')
            ->get();

        if (count($noReturned) > 0) {
            return response()->json(['data' => $noReturned], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'No hay bases en Gesi'], Response::HTTP_NOT_FOUND);
        }
    }

    public function activate(Format $format)
    {
        try {
            $format->update(['is_active' => 1]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'No se pudo activar el formato.')->withInput();
        }

        return redirect()->back()->with('success', 'Formato activado correctamente.');
    }

    public function deactivate(Format $format)
    {
        try {
            $format->update(['is_active' => 0]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'No se pudo desactivar el formato.')->withInput();
        }

        return redirect()->back()->with('success', 'Formato desactivado correctamente.');
    }

    public function format_sds($environment)
    {
        $formatsSds = FormatSds::where('environment_id', $environment)->get();
        return response()->json(['formatsSds' => $formatsSds], Response::HTTP_OK);
    }

    public function formatsAssociateSon(Request $request)
    {
        $request->validate([
            'format_id_son' => 'required|exists:formats,id',
            'format_id_dad' => 'required|exists:formats,id',
        ]);

        try {
            $update = AdicionalFormat::create([
                'id_format' => $request->input('format_id_dad'),
                'id_format_adicional' => $request->input('format_id_son'),
            ]);

            if (!$update) {
                return redirect()->back()->with('error', 'No se pudo asociar el formato.');
            }

            return redirect()->back()->with('success', 'Formatos asociados correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al asociar el formato.')->withInput();

        }
    }
}