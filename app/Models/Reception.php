<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reception extends Model
{
    use HasFactory;

    protected $fillable = [
        'environment',
        'file_number',
        'format_id',
        'quantity',
        'delivered_by',
        'received_by',
        'package_id',
        'typed_by',
        'typed_date',
        'type',
        'return_date',
        'returned_by',
        'localidad_id',
        'status',
        'batch_number',
        'received_by_environment',
        'required_date',
        'order_file',
        'consecutivo',
        'fecha_intervencion',
        'quantity_users',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'delivered_by', 'id');
    }

    public function user_receive()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function user_return()
    {
        return $this->belongsTo(User::class, 'returned_by', 'id');
    }

    public function user_receive_env()
    {
        return $this->belongsTo(User::class, 'received_by_environment', 'id');
    }

    public function bases()
    {
        return $this->belongsTo(Format::class, 'format_id', 'id');
    }

    public function packages()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function environment_file()
    {
        return $this->belongsTo(Entorno::class, 'environment', 'id');
    }

    public function typedBy()
    {
        return $this->belongsTo(User::class, 'typed_by');
    }

    public function correcciones()
    {
        return $this->hasMany(Correction::class, 'id_receptions');
    }

    public function consecutivos()
    {
        return $this->hasMany(Consecutivo::class, 'id_ficha', 'id');
    }

    public function productivity()
    {
        return $this->hasOne(Productivity::class, 'file_id');
    }

    public function interventionType()
    {
        return $this->belongsTo(InterventionType::class, 'intervention_type_id');
    }

    public function adicionalFormat()
    {
        return $this->hasMany(AdicionalFormat::class, 'id_format', 'format_id');
    }

    public function localidad()
    {
        return $this->belongsTo(Localidades::class, 'localidad_id', 'id');
    }

    public function adicionales()
    {
        return $this->hasManyThrough(
            Reception::class,             // Modelo final (los hijos)
            AdicionalFormat::class,       // Tabla puente
            'id_format',                  // Clave foránea en adicional_format → padre
            'format_id',                  // Clave en Reception → hijo
            'format_id',                  // Clave en Reception → padre
            'id_format_adicional'         // Clave en adicional_format → hijo
        );
    }

    public function status_histories()
    {
        return $this->hasMany(ReceptionStatusHistory::class, 'reception_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($reception) {
            $originalStatus = $reception->getOriginal('status');
            if ($originalStatus !== $reception->status) {
                $userId = Auth::id();

                ReceptionStatusHistory::create([
                    'reception_id' => $reception->id,
                    'previous_status' => $originalStatus,
                    'new_status' => $reception->status,
                    'changed_by' => $userId
                ]);
            }
        });
    }
  
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'reception_user',   // tabla pivot
            'reception_id',     // FK de Reception en la pivot
            'user_id'           // FK de User en la pivot
        )->withTimestamps();
    }

}
