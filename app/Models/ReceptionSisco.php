<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceptionSisco extends Model
{
    protected $table = 'reception_sisco';

    protected $fillable = [
        'localidad_id',
        'environment_id',
        'subnet_id',
        'format_id',
        'package_id',
        'delivered_by',
        'consecutivo',
        'batch_number',
        'intervention_date',
        'status',
        'order_file',
        'consecutivo_sisco',
    ];

    public function environment()
    {
        return $this->belongsTo(Entorno::class, 'environment_id');
    }

    public function format()
    {
        return $this->belongsTo(Format::class, 'format_id');
    }

    public function packages()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function localidad()
    {
        return $this->belongsTo(Localidades::class, 'localidad_id');
    }

    public function subnet()
    {
        return $this->belongsTo(Subnets::class, 'subnet_id');
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function statusHistories()
    {
        return $this->hasMany(ReceptionStatusHistory::class, 'reception_sisco_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'reception_user',   // tabla pivot
            'reception_sisco_id',     // FK de Reception en la pivot
            'user_id'           // FK de User en la pivot
        )->withTimestamps();
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            1 => '<span class="badge bg-success">Activo</span>',
            2 => '<span class="badge bg-warning">Generado</span>',
            3 => '<span class="badge bg-info">En proceso de entrega a GESI</span>',
            4 => '<span class="badge bg-primary">Entregado a GESI</span>',
            default => '<span class="badge bg-secondary">Otro</span>',
        };
    }
}
