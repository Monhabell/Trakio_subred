<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateReportActivity extends Model
{
    use HasFactory;

    protected $table = 'templates_report_activities';

    protected $fillable = [
        'name',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function textsTemplate(){
        return $this->hasMany(TextActivityTemplate::class);
    }
}
