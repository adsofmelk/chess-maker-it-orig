<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmSubscription extends Mailable
{
    use Queueable, SerializesModels;

    protected $greeting = '';
    protected $url = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->greeting = $data['greeting'];
        $this->url = $data['url'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.boletin.confirmed', [
            'level' => '',
            'greeting' => $this->greeting,
            'actionText' => 'Confirmar suscripción',
            'actionUrl' => $this->url,
            'line_down' => 'Si no realizaste esta solicitud, no se requiere realizar ninguna otra acción.'
        ])->subject('Confirmar suscripción al boletin de Chess make it');
    }
}
