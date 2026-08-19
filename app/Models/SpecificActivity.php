<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificActivity extends Model
{
    use HasFactory;

    protected $table = 'specific_activities';

    protected $fillable = [        
        'description',
        'number_activity',
        'environment_id'
    ];

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function environment(){
        return $this->belongsTo(Entorno::class, 'environment_id', 'id');
    }
}
