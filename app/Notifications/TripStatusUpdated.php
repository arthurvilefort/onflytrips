<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class TripStatusUpdated extends Notification
{
    use Queueable;

    private $trip;
    private $status;

    public function __construct($trip)
    {
        $this->trip = $trip;
        $this->status = $trip->status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Enviar para o BD e por e-mail
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Atualização no Status da Viagem')
                    ->greeting("Olá, {$notifiable->name}")
                    ->line("O status da sua viagem para {$this->trip->destination} foi atualizado para **{$this->status}**.")
                    ->action('Ver Detalhes', url("/api/trips/{$this->trip->id}"))
                    ->line('Se você não solicitou essa mudança, entre em contato conosco.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Sua viagem para {$this->trip->destination} foi atualizada para {$this->status}.",
            'trip_id' => $this->trip->id,
            'status' => $this->status,
        ];
    }
}
