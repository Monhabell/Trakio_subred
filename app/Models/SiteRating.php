<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteRating extends Model
{
    protected $fillable = ['user_id', 'rating', 'comment'];
}
