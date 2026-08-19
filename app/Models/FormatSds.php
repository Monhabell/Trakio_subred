<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatSds extends Model
{

    protected $table = 'formats_sds';

    protected $fillable = [
        'id_sds',
        'name_sds',
    ];

    public function formats()
    {
        return $this->belongsToMany(
            Format::class,               // modelo relacionado
            'formats_productivity_sds',  // tabla pivote
            'format_sds_id',             // columna local
            'format_id'                  // columna relacionada
        );
    }
}
