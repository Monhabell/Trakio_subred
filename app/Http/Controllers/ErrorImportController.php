<?php

namespace App\Http\Controllers;

use App\Models\Entorno;
use App\Models\ErrorField;
use App\Models\Format;
use App\Models\ErrorImport;
use App\Models\ErrorRecord;
use App\Models\ErrorRecordField;
use App\Models\Reception;
use App\Models\User;
use App\Services\ErrorSheetParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ErrorImportController extends Controller
{
    private const TEMP_DIR = 'error-imports';

    public function __construct(private ErrorSheetParser $parser)
    {
    }

    public function index()
    {
        $imports = ErrorImport::with(['environment', 'format', 'uploadedBy'])
            ->withCount([
                'recordFields as total_fields_count',
                'recordFields as pending_fields_count' => fn ($q) => $q->whereNull('resolved_at'),
            ])
            ->orderByDesc('id')
            ->get();

        return view('adm.error-imports.index', [
            'imports' => $imports,
            'formats' => Format::where('is_active', true)->orderBy('name')->get(),
            'totalPending' => ErrorRecordField::whereNull('resolved_at')->count(),
            'digitadoresByImport' => $this->digitadoresByImport($imports->pluck('id')),
        ]);
    }

    /**
     * Por cada carga, agrupa el avance por digitador (usuario asignado a la
     * ficha) para que el admin vea de un vistazo quién ya terminó, quién va
     * a medias y quién todavía no ha corregido ningún campo, junto con el
     * detalle de qué errores (ficha + campo) le quedan pendientes.
     */
    private function digitadoresByImport($importIds)
    {
        $records = ErrorRecord::with(['recordFields.field'])
            ->whereIn('error_import_id', $importIds)
            ->get();

        $users = User::whereIn('id', $records->pluck('user_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return $records->groupBy('error_import_id')->map(function ($importRecords) use ($users) {
            return $importRecords
                ->groupBy(fn ($record) => $record->user_id ?: 'raw:' . $record->username_raw)
                ->map(function ($userRecords) use ($users) {
                    $first = $userRecords->first();
                    $user = $first->user_id ? $users->get($first->user_id) : null;

                    $allFields = $userRecords->flatMap->recordFields;
                    $totalFields = $allFields->count();
                    $resolvedFields = $allFields->whereNotNull('resolved_at')->count();
                    $pending = $totalFields - $resolvedFields;

                    $pendingErrors = $userRecords->map(function ($record) {
                        $pendingFields = $record->recordFields->whereNull('resolved_at');

                        if ($pendingFields->isEmpty()) {
                            return null;
                        }

                        return [
                            'file_number' => $record->file_number,
                            'fields' => $pendingFields->map(fn ($field) => [
                                'label' => $field->field->label ?? 'Campo',
                                'issue' => $field->issue_label,
                            ])->values(),
                        ];
                    })->filter()->values();

                    return [
                        'name' => $user ? properNouns($user->name, $user->last_name) : ($first->username_raw ?: 'Sin identificar'),
                        'total_fields' => $totalFields,
                        'resolved_fields' => $resolvedFields,
                        'pending_fields' => $pending,
                        'status' => $pending <= 0 ? 'completado' : ($resolvedFields > 0 ? 'en_proceso' : 'sin_iniciar'),
                        'pending_errors' => $pendingErrors,
                    ];
                })
                ->sortBy('name')
                ->values();
        });
    }

    public function updateMeta(Request $request, ErrorImport $errorImport)
    {
        $request->validate([
            'format_id' => 'required|exists:formats,id',
            'section' => 'required|string|max:255',
        ]);

        $errorImport->update([
            'format_id' => $request->format_id,
            'section' => $request->section,
        ]);

        return back()->with('success', 'Base y sección actualizadas.');
    }

    public function destroy(ErrorImport $errorImport)
    {
        // Las FK de error_records y error_record_fields tienen onDelete('cascade'),
        // así que basta con borrar el ErrorImport para borrar toda la carga.
        $errorImport->delete();

        return back()->with('success', 'Carga eliminada. Ya puedes volver a subirla.');
    }

    public function testView()
    {
        return view('adm.error-imports.test', [
            'environments' => Entorno::where('id', '!=', 0)->get(),
            'formats' => Format::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'environment_id' => 'required|exists:environments,id',
            'format_id' => 'required|exists:formats,id',
            'section' => 'required|string|max:255',
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $token = (string) Str::uuid();
        $request->file('file')->storeAs(self::TEMP_DIR, $token . '.xlsx', 'local');

        $preview = $this->buildPreview($this->tempFilePath($token), (int) $request->environment_id);

        return view('adm.error-imports.test', [
            'environments' => Entorno::where('id', '!=', 0)->get(),
            'formats' => Format::where('is_active', true)->orderBy('name')->get(),
            'environment_id' => (int) $request->environment_id,
            'format_id' => (int) $request->format_id,
            'section' => $request->section,
            'token' => $token,
            'original_filename' => $request->file('file')->getClientOriginalName(),
            'preview' => $preview,
        ]);
    }

    /**
     * Recibe los issues ya detectados por el validador del cliente (validator.blade.php)
     * y crea un ErrorImport + ErrorRecord/ErrorRecordField por ficha, igual que el flujo
     * manual de subir un Excel, pero sin re-leer ningún archivo ni depender de colores de
     * celda: los datos ya vienen agrupados por ficha/campo desde el navegador.
     */
    public function commitFromValidator(Request $request)
    {
        $request->validate([
            'environment_id' => 'required|exists:environments,id',
            'original_filename' => 'required|string|max:255',
            'format_nombre' => 'nullable|string|max:255',
            'rows' => 'required|array|min:1',
            'rows.*.file_number' => 'required|string',
            'rows.*.username_raw' => 'nullable|string',
            'rows.*.row_number' => 'nullable|integer',
            'rows.*.fields' => 'required|array|min:1',
            'rows.*.fields.*.label' => 'required|string',
            'rows.*.fields.*.value' => 'nullable|string',
            'rows.*.fields.*.observacion' => 'nullable|string',
        ]);

        $environmentId = (int) $request->environment_id;
        $rows = $request->input('rows');
        $formatNombre = trim((string) $request->input('format_nombre'));
        $filename = $formatNombre !== ''
            ? $formatNombre . ' — ' . $request->original_filename
            : $request->original_filename;

        $errorImport = DB::transaction(function () use ($rows, $environmentId, $filename) {
            $errorImport = ErrorImport::create([
                'environment_id' => $environmentId,
                'format_id' => null,
                'section' => null,
                'uploaded_by' => Auth::id(),
                'original_filename' => $filename,
                'total_rows' => count($rows),
                'flagged_rows' => count($rows),
                'status' => 'committed',
                'committed_at' => now(),
            ]);

            $fieldIds = [];

            foreach ($rows as $row) {
                $usernameRaw = $row['username_raw'] ?? '';
                $user = $usernameRaw !== ''
                    ? User::where('username', $usernameRaw)->first()
                    : null;
                $reception = Reception::where('environment', $environmentId)
                    ->where('file_number', $row['file_number'])
                    ->first();

                $record = ErrorRecord::create([
                    'error_import_id' => $errorImport->id,
                    'environment_id' => $environmentId,
                    'reception_id' => $reception?->id,
                    'file_number' => $row['file_number'],
                    'user_id' => $user?->id,
                    'username_raw' => $usernameRaw,
                    'row_number' => $row['row_number'] ?? 0,
                ]);

                foreach ($row['fields'] as $field) {
                    $label = trim((string) $field['label']);
                    if ($label === '') {
                        continue;
                    }

                    if (!isset($fieldIds[$label])) {
                        $fieldIds[$label] = ErrorField::firstOrCreate([
                            'environment_id' => $environmentId,
                            'label' => $label,
                        ])->id;
                    }

                    ErrorRecordField::create([
                        'error_record_id' => $record->id,
                        'error_field_id' => $fieldIds[$label],
                        'value' => $field['value'] ?? '',
                        'observacion' => $field['observacion'] ?? null,
                    ]);
                }
            }

            return $errorImport;
        });

        return response()->json([
            'success' => true,
            'error_import_id' => $errorImport->id,
            'fichas' => count($rows),
        ]);
    }

    public function commit(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'environment_id' => 'required|exists:environments,id',
            'format_id' => 'required|exists:formats,id',
            'section' => 'required|string|max:255',
            'original_filename' => 'required|string',
        ]);

        $environmentId = (int) $request->environment_id;
        $filePath = $this->tempFilePath($request->token);

        if (!file_exists($filePath)) {
            return back()->with('error', 'El archivo temporal ya no existe, vuelve a subirlo.');
        }

        $parsed = $this->parser->parse($filePath);

        DB::transaction(function () use ($parsed, $environmentId, $request) {
            $errorImport = ErrorImport::create([
                'environment_id' => $environmentId,
                'format_id' => (int) $request->format_id,
                'section' => $request->section,
                'uploaded_by' => Auth::id(),
                'original_filename' => $request->original_filename,
                'total_rows' => $parsed['stats']['total_rows'],
                'flagged_rows' => $parsed['stats']['flagged_rows'],
                'status' => 'committed',
                'committed_at' => now(),
            ]);

            $flaggedLabels = collect($parsed['rows'])
                ->flatMap(fn ($row) => collect($row['flags'])->pluck('field_label'))
                ->unique();

            $fieldIds = [];
            foreach ($flaggedLabels as $label) {
                $fieldIds[$label] = ErrorField::firstOrCreate([
                    'environment_id' => $environmentId,
                    'label' => $label,
                ])->id;
            }

            foreach ($parsed['rows'] as $row) {
                $user = User::where('username', $row['username_raw'])->first();
                $reception = Reception::where('environment', $environmentId)
                    ->where('file_number', $row['file_number'])
                    ->first();

                $record = ErrorRecord::create([
                    'error_import_id' => $errorImport->id,
                    'environment_id' => $environmentId,
                    'reception_id' => $reception?->id,
                    'file_number' => $row['file_number'],
                    'user_id' => $user?->id,
                    'username_raw' => $row['username_raw'],
                    'location_reference' => $row['location_reference'] ?: null,
                    'observations' => $row['observations'] ?: null,
                    'row_number' => $row['row_number'],
                ]);

                foreach ($row['flags'] as $flag) {
                    ErrorRecordField::create([
                        'error_record_id' => $record->id,
                        'error_field_id' => $fieldIds[$flag['field_label']],
                        'value' => $flag['value'],
                    ]);
                }
            }
        });

        Storage::disk('local')->delete(self::TEMP_DIR . '/' . $request->token . '.xlsx');

        return redirect()->route('adm.error-imports.test')
            ->with('success', 'Errores cargados correctamente: ' . $parsed['stats']['flagged_rows'] . ' fichas.');
    }

    private function tempFilePath(string $token): string
    {
        return Storage::disk('local')->path(self::TEMP_DIR . '/' . $token . '.xlsx');
    }

    private function buildPreview(string $filePath, int $environmentId): array
    {
        $parsed = $this->parser->parse($filePath);

        $rows = collect($parsed['rows'])->map(function ($row) use ($environmentId) {
            $user = User::where('username', $row['username_raw'])->first();
            $reception = Reception::where('environment', $environmentId)
                ->where('file_number', $row['file_number'])
                ->first();

            return [
                'file_number' => $row['file_number'],
                'username_raw' => $row['username_raw'],
                'user_name' => $user ? trim($user->name . ' ' . $user->last_name) : null,
                'reception_found' => (bool) $reception,
                'location_reference' => $row['location_reference'],
                'observations' => $row['observations'],
                'flags' => $row['flags'],
            ];
        });

        return [
            'fields' => $parsed['fields'],
            'rows' => $rows,
            'stats' => $parsed['stats'],
            'username_column_found' => $parsed['username_column_found'],
            'location_column_found' => $parsed['location_column_found'],
            'observations_column_found' => $parsed['observations_column_found'],
            'unresolved_users' => $rows->whereNull('user_name')->count(),
            'unresolved_fichas' => $rows->where('reception_found', false)->count(),
        ];
    }
}
