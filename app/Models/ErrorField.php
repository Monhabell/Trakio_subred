<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorField extends Model
{
    protected $fillable = [
        'environment_id',
        'label',
    ];

    public function environment()
    {
        return $this->belongsTo(Entorno::class, 'environment_id');
    }

    public function recordFields()
    {
        return $this->hasMany(ErrorRecordField::class);
    }
}
