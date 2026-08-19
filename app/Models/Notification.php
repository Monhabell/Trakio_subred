<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'message',
        'type',
        'status',
        'notifiable_type',
        'notifiable_id',
        'read_at',
        'other_time_id',
        'consecutivo_permission_id',
        'data'
    ];

    public function fromUser(){
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }

    public function toUser(){
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    public function otherTimes(){
        return $this->belongsTo(OtherTime::class, 'other_time_id', 'id');
    }

    public function consecutivoPermission(){
        return $this->belongsTo(ConsecutivoPermission::class, 'consecutivo_permission_id', 'id');
    }

    public function dataUser(){
        return $this->belongsTo(DataUser::class, 'from_user_id', 'id_user');
    }
}