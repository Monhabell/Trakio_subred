<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GesiDeliveryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $deadlineDate,
        public readonly string $currentMonthName,
        public readonly array $delayedFichas = [],
        public readonly bool $isEnvironmentAdmin = false,
        public readonly bool $isGesiAdmin = false,
        public readonly int $totalPendingCount = 0,
        public readonly string $professionalDeadlineDate = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠ Recordatorio entrega de bases GESI — ' . $this->currentMonthName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.gesi-delivery-reminder',
        );
    }
}
