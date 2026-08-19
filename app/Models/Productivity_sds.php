<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productivity_sds extends Model
{
    public $timestamps = false;
    
    use HasFactory;
    protected $table = "productivity_calculation";

    protected $fillable = [
        'updated_at',
        'sds_id',
        'file_number',
        'dig_id',
        'calculation_time',
        'inactivity_time',
        'user_sds',
        'pages',
        'active_page'
    ];
    
}