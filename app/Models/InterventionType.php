<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_format',
    ];

    /**
     * Relación con Format
     */
    public function format()
    {
        return $this->belongsTo(Format::class, 'id_format');
    }
}
