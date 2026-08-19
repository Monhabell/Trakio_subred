<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorImport extends Model
{
    protected $fillable = [
        'environment_id',
        'format_id',
        'section',
        'uploaded_by',
        'original_filename',
        'total_rows',
        'flagged_rows',
        'status',
        'committed_at',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
    ];

    public function environment()
    {
        return $this->belongsTo(Entorno::class, 'environment_id');
    }

    public function format()
    {
        return $this->belongsTo(Format::class, 'format_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function records()
    {
        return $this->hasMany(ErrorRecord::class);
    }

    public function recordFields()
    {
        return $this->hasManyThrough(ErrorRecordField::class, ErrorRecord::class, 'error_import_id', 'error_record_id');
    }
}
