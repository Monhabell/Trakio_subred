<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsecutivoPermissionApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly int $durationMinutes,
        public readonly string $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Permiso aprobado — Consecutivos del mes anterior',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consecutivo-permission-approved',
        );
    }
}
