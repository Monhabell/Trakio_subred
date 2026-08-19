<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorRecord extends Model
{
    protected $fillable = [
        'error_import_id',
        'environment_id',
        'reception_id',
        'file_number',
        'user_id',
        'username_raw',
        'location_reference',
        'observations',
        'row_number',
    ];

    public function errorImport()
    {
        return $this->belongsTo(ErrorImport::class);
    }

    public function environment()
    {
        return $this->belongsTo(Entorno::class, 'environment_id');
    }

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordFields()
    {
        return $this->hasMany(ErrorRecordField::class);
    }

    public function pendingFields()
    {
        return $this->hasMany(ErrorRecordField::class)->whereNull('resolved_at');
    }
}
