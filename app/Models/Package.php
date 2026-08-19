<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_package',
        'status',
        'environment_id',
        'observations',
        'return_digitator',
        'type',
        'start_at',
        'end_at'
    ];

    public function receptions(){
        return $this->hasMany(Reception::class, 'package_id');
    }

    public function assignments(){
        return $this->hasMany(Assignment::class, 'package_id');
    }

    public function environment(){
        return $this->belongsTo(Entorno::class, 'environment_id');
    }

    public function getTotalHoursAttribute()
    {
        return $this->receptions->sum(function ($reception) {

            $format = $reception->bases;

            if (!$format) {
                return 0;
            }

            $users = filled($reception->quantity_users)
                ? $reception->quantity_users
                : $reception->quantity;

            return $format->time_header +
                ($format->body_time * $users);
        });
    }
}