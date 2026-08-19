<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'productivity';

    protected $fillable = [
        'dig_id',
        'file_id',
        'base_id',
        'observations',
        'quantity_users',
        'type'
    ];

    public function receptions(){
        return $this->belongsTo(Reception::class, 'file_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'dig_id', 'id');
    }

    public function dataUser(){
        return $this->belongsTo(DataUser::class, 'dig_id', 'id_user');
    }

    public function base(){
        return $this->belongsTo(Format::class, 'base_id', 'id');
    }

    /**
     * Observaciones de fichas que pertenecen al usuario (vía reception_user),
     * quedándonos solo con la más reciente por cada ficha/formato: una misma
     * ficha genera un nuevo registro de productividad cada vez que se actualiza
     * la recepción, así que las anteriores ya no son la observación vigente.
     */
    public static function latestObservationsForUser($userId, $start, $end)
    {
        return static::query()
            ->whereNotNull('observations')
            ->where('observations', '!=', '')
            ->whereBetween('productivity.created_at', [$start, $end])
            ->whereHas('receptions.users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->with(['receptions.bases', 'user'])
            ->orderByDesc('productivity.created_at')
            ->get()
            ->unique(fn ($observation) => $observation->receptions->file_number.'-'.$observation->receptions->format_id)
            ->values();
    }
}