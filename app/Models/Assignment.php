<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'digitizer_id',
        'package_id'
    ];

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'digitizer_id', 'id');
    }
}