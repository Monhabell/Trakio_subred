<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\ConsecutivoPermission;
use App\Models\Notification;
use App\Models\User;

// Mail
use App\Mail\ConsecutivoPermissionApprovedMail;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Request
use App\Http\Requests\StoreConsecutivoPermissionRequest;

class ConsecutivoPermissionController extends Controller
{
    public function store(StoreConsecutivoPermissionRequest $request)
    {
        try {
            $userAuth = Auth::user();

            $tieneSolicitudPendiente = ConsecutivoPermission::where('user_id', $userAuth->id)
                ->where('status', ConsecutivoPermission::STATUS_PENDING)
                ->exists();

            if ($tieneSolicitudPendiente) {
                return redirect()->back()->with('error', 'Ya tiene una solicitud pendiente de aprobación.');
            }

            $permission = ConsecutivoPermission::create([
                'user_id' => $userAuth->id,
                'requested_quantity' => $request->requested_quantity,
                'reason' => $request->reason,
                'status' => ConsecutivoPermission::STATUS_PENDING,
            ]);

            $admins = User::where('is_admin', 1)->where('environment_id', 0)->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'from_user_id' => $userAuth->id,
                    'to_user_id' => $admin->id,
                    'message' => 'solicita permiso para registrar ' . $request->requested_quantity .
                        ' consecutivo(s) del mes anterior' . ($request->reason ? ', motivo: ' . $request->reason : ''),
                    'type' => 'consecutivo-permission-request',
                    'status' => 0,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $admin->id,
                    'consecutivo_permission_id' => $permission->id,
                ]);
            }

            return redirect()->back()->with('success', 'Solicitud enviada correctamente. Cuando sea aprobada, recibirá una notificación en el sistema y un correo indicando cuánto tiempo tiene disponible.');
        } catch (\Throwable $th) {
            Log::error('Error creando solicitud de permiso de consecutivos: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Ha ocurrido un error, por favor intente nuevamente');
        }
    }

    public function approve(ConsecutivoPermission $consecutivoPermission, Request $request)
    {
        try {
            $request->validate([
                'duration_minutes' => 'required|integer|min:1|max:120',
            ]);

            $expiresAt = now()->addMinutes((int) $request->duration_minutes);

            $consecutivoPermission->update([
                'status' => ConsecutivoPermission::STATUS_APPROVED,
                'admin_id' => Auth::id(),
                'duration_minutes' => $request->duration_minutes,
                'approved_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            Notification::where('consecutivo_permission_id', $consecutivoPermission->id)
                ->update(['status' => 1, 'read_at' => now()]);

            $solicitante = $consecutivoPermission->user;

            Notification::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $solicitante->id,
                'message' => 'aprobó tu solicitud: tienes ' . $request->duration_minutes .
                    ' minutos para registrar consecutivos del mes anterior',
                'type' => 'consecutivo-permission-response',
                'status' => 0,
                'notifiable_type' => User::class,
                'notifiable_id' => $solicitante->id,
                'consecutivo_permission_id' => $consecutivoPermission->id,
            ]);

            try {
                Mail::to($solicitante->email)->send(new ConsecutivoPermissionApprovedMail(
                    $solicitante->name,
                    (int) $request->duration_minutes,
                    $expiresAt->format('d/m/Y h:i A')
                ));
            } catch (\Throwable $th) {
                Log::error('Error enviando correo de permiso de consecutivos aprobado: ' . $th->getMessage());
            }

            return response()->json(['message' => 'Permiso aprobado correctamente'], 200);
        } catch (\Throwable $th) {
            Log::error('Error aprobando permiso de consecutivos: ' . $th->getMessage());
            return response()->json(['error' => 'Error al aprobar el permiso'], 500);
        }
    }

    public function reject(ConsecutivoPermission $consecutivoPermission)
    {
        try {
            $consecutivoPermission->update([
                'status' => ConsecutivoPermission::STATUS_REJECTED,
                'admin_id' => Auth::id(),
            ]);

            Notification::where('consecutivo_permission_id', $consecutivoPermission->id)
                ->update(['status' => 1, 'read_at' => now()]);

            $solicitante = $consecutivoPermission->user;

            Notification::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $solicitante->id,
                'message' => 'rechazó tu solicitud de permiso para registrar consecutivos del mes anterior',
                'type' => 'consecutivo-permission-response',
                'status' => 0,
                'notifiable_type' => User::class,
                'notifiable_id' => $solicitante->id,
                'consecutivo_permission_id' => $consecutivoPermission->id,
            ]);

            return response()->json(['message' => 'Permiso rechazado'], 200);
        } catch (\Throwable $th) {
            Log::error('Error rechazando permiso de consecutivos: ' . $th->getMessage());
            return response()->json(['error' => 'Error al rechazar el permiso'], 500);
        }
    }

    public function status()
    {
        $permission = ConsecutivoPermission::activeFor(Auth::id())
            ->orderByDesc('expires_at')
            ->first();

        if (!$permission) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'expires_at' => $permission->expires_at->toIso8601String(),
            'minutes_left' => now()->diffInMinutes($permission->expires_at),
        ]);
    }
}
