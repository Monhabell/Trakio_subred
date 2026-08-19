<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformePlanilla extends Model
{
    use HasFactory;

    protected $table = 'informe_planilla';

    protected $fillable = [
        'id_user',
        'mesCertificacion',
        'numPlanilla',
        'fechaPago',
        'valorEps',
        'valorAfp',
        'arlValor',
        'valorCompensacion',
        'totalPlanilla',
        'cargueDoc',
        'año'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}