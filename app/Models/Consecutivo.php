<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consecutivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_ficha',
        'localidad',
        'up7',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'consecutivo',
    ];

    public function receptions()
    {
        return $this->belongsTo(Reception::class, 'id_ficha', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}