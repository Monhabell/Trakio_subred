<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceptionStatusHistory extends Model
{
    public $fillable = [
        'reception_id',
        'previous_status',
        'new_status',
        'changed_by'
    ];

    public function reception()
    {
        return $this->belongsTo(Reception::class, 'reception_id', 'id');
    }

    public function receptionSisco()
    {
        return $this->belongsTo(ReceptionSisco::class, 'reception_sisco_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
}
