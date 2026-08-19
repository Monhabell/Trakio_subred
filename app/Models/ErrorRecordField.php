<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorRecordField extends Model
{
    protected $fillable = [
        'error_record_id',
        'error_field_id',
        'value',
        'observacion',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function record()
    {
        return $this->belongsTo(ErrorRecord::class, 'error_record_id');
    }

    public function field()
    {
        return $this->belongsTo(ErrorField::class, 'error_field_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getIssueLabelAttribute(): string
    {
        if (trim((string) $this->observacion) !== '') {
            return $this->observacion;
        }

        return trim((string) $this->value) === '' ? 'Campo vacío' : 'Campo con dato incorrecto';
    }
}
