<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LicenseController extends Controller
{
    public function validateLicense(Request $request)
    {
        try {
            $license = License::where('license_key', $request->key)
                ->where('program_name', $request->program)
                ->first();

            // 1. Si no existe o no está activa, error 403
            if (!$license || !$license->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Licencia no válida o inactiva.'
                ], 403);
            }

            // 2. Validar vencimiento (Arreglado de forma ultra segura)
            if (!empty($license->expires_at)) {
                // Carbon::parse transforma el string de la BBDD en un objeto operable
                $expirationDate = Carbon::parse($license->expires_at);

                if (now()->gt($expirationDate)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'La licencia ha expirado el ' . $expirationDate->format('d/m/Y')
                    ], 403);
                }
            }

            // 3. Validar o registrar HWID (Pasándole un valor por defecto si Python no envía HWID)
            $hwid = $request->input('hwid', 'DESKTOP_UNKNOWN');

            if (empty($license->hwid)) {
                $license->update(['hwid' => $hwid]);
            } elseif ($license->hwid !== $hwid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Esta licencia ya está vinculada a otro equipo.'
                ], 403);
            }

            // 4. Respuesta exitosa para Python
            return response()->json([
                'status'           => 'success',
                'message'          => 'Licencia validada correctamente.',
                'client_name'      => $license->client_name ?? 'Cliente',
                'plan'             => $license->plan_name ?? 'free',
                'tokens_remaining' => (int)($license->tokens_available ?? 0),
                'hc'               => (bool)($license->has_hc ?? false),
                'gesiform'         => (bool)($license->has_gesiform ?? false),
            ], 200);
        } catch (\Exception $e) {
            // Si algo falla, este catch evitará el error 500 genérico y te dirá exactamente qué pasó
            return response()->json([
                'status' => 'error',
                'message' => 'Error interno en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
