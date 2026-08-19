<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdicionalFormat extends Model
{
    use HasFactory;

    protected $table = 'adicional_format';

    protected $fillable = [
        'id_format',
        'id_format_adicional',
    ];

    /**
     * Relación con el formato principal
     */
    public function format()
    {
        return $this->belongsTo(Format::class, 'id_format');
    }

    /**
     * Relación con el formato adicional
     */
    public function formatAdicional()
    {
        return $this->belongsTo(Format::class, 'id_format_adicional');
    }
}
