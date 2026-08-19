<?php

namespace App\Mail;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnviarInformeSemanal extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath11;
    public $filePath22;
    public $asunto;

    public function __construct($filePath11, $filePath22, $asunto)
    {
        $this->filePath11 = $filePath11;
        $this->filePath22 = $filePath22;
        $this->asunto = $asunto;
    }

    public function build()
    {
        $email = $this->subject($this->asunto)
                      ->view('emails.enviar_pdf');
        
        // Adjuntar los dos archivos
        if (file_exists($this->filePath11)) {
            $email->attach($this->filePath11);
        } else {
            Log::error("Archivo no encontrado: " . $this->filePath11);
        }

        if (file_exists($this->filePath22)) {
            $email->attach($this->filePath22);
        } else {
            Log::error("Archivo no encontrado: " . $this->filePath22);
        }

        return $email;
    }
}
