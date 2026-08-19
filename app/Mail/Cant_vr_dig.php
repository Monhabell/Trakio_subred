<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Cant_vr_dig extends Mailable
{
    use Queueable, SerializesModels;

    public $variable1;
    public $variable2;
    public $variable3;
    public $variable4;
    public $variable5;
    public $variable6;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($variable1, $variable2, $variable3, $variable4, $variable6)
    {
        $this->variable1 = $variable1;
        $this->variable2 = $variable2;
        $this->variable3 = $variable3;
        $this->variable4 = $variable4;
        $this->variable6 = $variable6;


    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.recibed_vs_digitizer')
                    ->subject('Reporte VS Digitizer')
                    ->with([
                        'variable1' => $this->variable1,
                        'variable2' => $this->variable2,
                        'variable3' => $this->variable3,
                        'variable4' => $this->variable4,
                        'variable6' => $this->variable6,
                    ]);
    }
}
