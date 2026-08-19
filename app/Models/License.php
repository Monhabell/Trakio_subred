<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{

    protected $fillable = [
        'user_id',
        'program_name',
        'plan_name',
        'license_key',
        'client_name',
        'type',
        'expires_at',
        'is_active',
        'has_hc',
        'has_gesiform',
        'tokens_available',
        'hwid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
