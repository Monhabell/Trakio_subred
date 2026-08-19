<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Localidades extends Model
{
    protected $table = 'localidades';

    protected $fillable = [
        'name',
        'identificador',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'localidad_id');
    }
}
