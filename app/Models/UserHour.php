<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHour extends Model
{
    use HasFactory;

    protected $table = 'user_hours';

    protected $fillable = [
        'user_id',
        'number_month',
        'year',
        'hours_per_month',
        'total_over_times'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}