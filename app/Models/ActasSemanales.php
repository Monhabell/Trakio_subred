<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActasSemanales extends Model
{
    use HasFactory;

    protected $table = 'weekly_tasks_acts';

    protected $fillable = [
        'subject',
        'days',
        'environment_id',
        'email',
    ];

    protected $casts = [
        'days' => 'array',
    ];

    public function environment(){
        return $this->belongsTo(Entorno::class, 'environment_id', 'id');
    }
}