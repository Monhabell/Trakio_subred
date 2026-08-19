<?php

namespace App\Notifications\Channels;

use App\Models\NotificacionCumpleaños;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;


class CustomDatabaseChannel
{
    public function send($notifiable, $notification)
{
    $data = $notification->toArray($notifiable);
    // Guarda la notificación en la tabla `notificacion_cumpleaños`
    NotificacionCumpleaños::create([
        'type' => get_class($notification),
        'notifiable_type' => get_class($notifiable),
        'notifiable_id' => $notifiable->id,
        'id_bithday' => $data['id_user']['id'],
        'data' => json_encode($data),
        'likes' => 0, // Asigna null si no es necesario
    ]);
}
}
