<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'name',
        'path',
        'file_year',
        'document_mes',
        'signature_date',
        'status',
        'email_to_send'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function scopePlanillaForUserInMonth($query, $userId, $month)
    {
        return $query->where('name', 'planilla')
                    ->where('id_user', $userId)
                    ->where('document_mes', intval($month));
    }

}