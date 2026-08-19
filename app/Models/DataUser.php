<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'document',
        'phone',
        'address',
        'rh',
        'contract',
        'birthdate',
        'sex',
        'ethnicity',
        'eps',
        'afp',
        'arl',
        'caja',
        'environment_assignment_id',
        'tema',
        'url_img'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}