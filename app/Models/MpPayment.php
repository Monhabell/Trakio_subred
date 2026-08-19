<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'user_id',
        'package',
        'amount',
        'status'
    ];
}
