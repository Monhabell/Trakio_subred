<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsecutivoPermission extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    protected $fillable = [
        'user_id',
        'admin_id',
        'requested_quantity',
        'reason',
        'status',
        'duration_minutes',
        'approved_at',
        'expires_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeActiveFor(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)
            ->where('status', self::STATUS_APPROVED)
            ->where('expires_at', '>', now());
    }
}
