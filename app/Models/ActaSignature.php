<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActaSignature extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'acta_signatures';

    protected $fillable = [
        'document_id',
        'user_id',
        'order',
        'is_signed',
        'signed_at',
        'signature_data',
    ];

    protected $casts = [
        'is_signed' => 'boolean',
        'signed_at' => 'datetime',
    ];

    /**
     * Relación con el Documento (Acta)
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Relación con el Usuario que firma
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
