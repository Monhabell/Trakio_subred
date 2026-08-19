<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OtherTime extends Model
{
    use HasFactory;

    protected $fillable = [
       'approved',
       'date_time',
       'request_to',
       'time',
       'id_user',
       'description'
    ];

    public function requestTo(){
        return $this->belongsTo(User::class, 'request_to', 'id');
    }

    public function userId(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function dataUser(){
        return $this->belongsTo(DataUser::class, 'id_user', 'id_user');
    }
}