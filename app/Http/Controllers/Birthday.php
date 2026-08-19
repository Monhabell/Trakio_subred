<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\DataUser;
use App\Models\NotificacionCumpleaños;
use App\Models\Notification;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Birthday extends Controller
{
    public function likes(Request $request)
    {
        // Verifica que UserBirthdays esté en el request
        if (!$request->has('UserBirthdays')) {
            return response()->json(['message' => 'UserBirthdays parameter missing'], 400);
        }

        $likesBirthday = $request->input('UserBirthdays');
        $userId = Auth::id(); // Suponiendo que el usuario está autenticado

        if (!$userId) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }


        // craer en la tabla notificacion
        Notification::create([
            'from_user_id' => $userId,
            'to_user_id' => $likesBirthday,
            'message' => 'Te ha felicitado en tus cumpleaños',
            'type' => 'Cumpleaños',
            'status' => 0,
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $likesBirthday,
            'other_time_id' => null,
        ]);
        

        // Obtener datos del usuario
        $userData = DataUser::where('id_user', $userId)->first();
        if (!$userData) {
            return response()->json(['message' => 'User data not found'], 404);
        }

        $userImageUrl = $userData->url_img;

        // Buscar todos los registros con el id de cumpleaños
        $notifications = NotificacionCumpleaños::where('id_bithday', $likesBirthday)->get();

        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $notification) {
                // Incrementar el conteo de likes
                $notification->likes = ($notification->likes ?? 0) + 1;

                // Obtener la lista de usuarios que han dado like
                $likedUsers = json_decode($notification->is_user_likes, true) ?: [];

                $userAlreadyLiked = false;
                foreach ($likedUsers as $likedUser) {
                    if ($likedUser['id'] === $userId) {
                        $userAlreadyLiked = true;
                        break;
                    }
                }

                if (!$userAlreadyLiked) {
                    $likedUsers[] = [
                        'id' => $userId,
                        'image_url' => $userImageUrl
                    ];
                    $notification->is_user_likes = json_encode($likedUsers);
                }

                // Guardar los cambios
                $notification->save();
            }

            return response()->json(['message' => 'Likes updated successfully', 'likedUsers' => $likedUsers]);
        } else {
            // Manejar el caso en que no se encuentran registros
            return response()->json(['message' => 'No notifications found'], 404);
        }
    }
}
