<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Correction extends Model
{
    use HasFactory;
    protected $table = 'correcciones';

    protected $fillable = [
        'corrected',
        'tipo_cambio',
        'id_reg',
        'no_pag',
        'nuevo_numero',
        'nueva_fecha',
        'id_receptions',
        'id_user'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function receptions(){
        return $this->belongsTo(Reception::class, 'id_receptions', 'id');
    }
}