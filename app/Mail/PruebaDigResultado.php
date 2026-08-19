<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PruebaDigResultado extends Mailable
{
    use Queueable, SerializesModels;

    public $digitador;
    public $score;
    public $totalSecs;
    public $fichas;
    public $mecanografia;

    public function __construct($digitador, $score, $totalSecs, $fichas, $mecanografia = null)
    {
        $this->digitador = $digitador;
        $this->score = $score;
        $this->totalSecs = $totalSecs;
        $this->fichas = $fichas;
        $this->mecanografia = $mecanografia;
    }

    public function build()
    {
        return $this->view('emails.prueba_dig_resultado')
            ->subject("Resultado prueba de digitación - {$this->digitador} ({$this->score}/100)");
    }
}
