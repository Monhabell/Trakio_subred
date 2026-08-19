<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hour_value',
        'total_fee'
    ];

    public function activities(){
        return $this->belongsToMany(SpecificActivity::class);
    }
}