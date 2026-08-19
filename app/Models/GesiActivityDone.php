<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GesiActivityDone extends Model
{
    use HasFactory;

    public $table = 'gesi_activities_done';

    protected $fillable = [
        'role_id',
        'description',
        'order_activity'
    ];
}
