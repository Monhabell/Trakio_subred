<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entorno extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'environments';

    public function formats(){
        return $this->hasMany(Format::class, 'environment_id');
    }

    public function packages(){
        return $this->hasMany(Package::class, 'environment_id');
    }
}