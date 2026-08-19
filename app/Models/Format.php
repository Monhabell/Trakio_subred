<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'header_time',
        'body_time',
        'environment_id',
        'is_active',
    ];

    public function entorno()
    {
        return $this->belongsTo(Entorno::class, 'environment_id', 'id');
    }

     /**
     * Relación con Intervenciones
     */
    public function intervencionesLab()
    {
        return $this->hasMany(InterventionType::class, 'id_format');
    }

    public function adicionales()
    {
        return $this->hasMany(AdicionalFormat::class, 'id_format');
    }

    public function adicionalesDe()
    {
        return $this->hasMany(AdicionalFormat::class, 'id_format_adicional');
    }

    // Relacion de muchos a muchos con format_sds
    public function format_sds()
    {
        return $this->belongsToMany(
            FormatSds::class,          // modelo relacionado
            'formats_productivity_sds',// tabla pivote
            'format_id',               // columna local
            'format_sds_id'            // columna relacionada
        );
    }


    public function formatosAdicionales()
    {
        return $this->belongsToMany(
            Format::class,        // modelo relacionado
            'adicional_format',   // tabla pivote
            'id_format',          // columna local
            'id_format_adicional' // columna relacionada
        );
    }
}