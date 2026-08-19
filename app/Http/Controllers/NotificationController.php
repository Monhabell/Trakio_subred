<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Notification;
use App\Models\User;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Request
use App\Http\Requests\StoreNotificationRequest;

class NotificationController extends Controller
{
    public function show()
    {
        try {
            $userAuth = Auth::User();            
            $notifications = Notification::with([
                'fromUser:id,name,last_name',
                'dataUser:id_user,url_img',
                'otherTimes',
                ])
            ->where('to_user_id', $userAuth->id)
            ->where('status', 0)
            ->orderBy('id', 'desc')
            ->get();

            return response()->json(['data' => $notifications]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener notificaciones'], 500);
        }
    }

    public function store(StoreNotificationRequest $request)
    {
        try {
            $userAuth = Auth::user();
            if($request->other_time_id){
                $this->updateStatusNotification('other_time_id', $request->other_time_id);
            }
            Notification::create([
                'from_user_id' => $userAuth->id,
                'to_user_id' => $request->to_user_id,
                'message' => $request->message,
                'type' => $request->type,
                'status' => 0,
                'notifiable_type' => User::class,
                'notifiable_id' => $request->to_user_id,
                'other_time_id' => $request->other_time_id,
            ]);
    
            return response()->json(['message' => 'Notificación creada.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear notificación'], 500);
        }
    }

    private function updateStatusNotification($field, $value){
        try {
            Notification::where($field, $value)->update([
               'status' => 1
            ]);
            return true;

        } catch (\Exception $e) {
            Log::error('Error al actualizar estatus de notificación'.$e->getMessage());
            return false;
        }
    }

    public function markAsRead(Notification $notification)
    {
        try {
            $notification->update([
                'read_at' => now(),
                'status' => 1
            ]);
    
            return response()->json(['message' => 'Notificación leida.'], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => 'Error al marcar como leída la notificación'], 500);
        }
    }
}