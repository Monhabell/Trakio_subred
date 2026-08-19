<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionCumpleaños extends Model
{
    protected $table = 'notificacion_cumpleaños';

    protected $fillable = [
        'type',
        'notifiable_id',
        'id_bithday',
        'notifiable_type',
        'data',
        'likes',
        'is_user_likes'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}