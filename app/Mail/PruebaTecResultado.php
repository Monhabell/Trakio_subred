<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PruebaTecResultado extends Mailable
{
    use Queueable, SerializesModels;

    public $candidato;
    public $score;
    public $totalSecs;
    public $modulos;

    public function __construct($candidato, $score, $totalSecs, $modulos)
    {
        $this->candidato = $candidato;
        $this->score = $score;
        $this->totalSecs = $totalSecs;
        $this->modulos = $modulos;
    }

    public function build()
    {
        return $this->view('emails.prueba_tec_resultado')
            ->subject("Resultado prueba técnico de sistemas - {$this->candidato} ({$this->score}/100)");
    }
}
