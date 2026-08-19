<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextActivityTemplate extends Model
{
    use HasFactory;

    protected $table = 'text_activities_templates';

    protected $fillable = [
        'description',
        'template_id',
        'position'
    ];

    public function template(){
        return $this->belongsTo(TemplateReportActivity::class, 'template_id');
    }
}
