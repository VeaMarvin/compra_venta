<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyAdmin extends Notification {

    use Queueable;
    public $datos_global;

    public function __construct( Array $datos ) {
        $this->datos_global = $datos;
    }

    public function via($notifiable) {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable) {
        return [
            'datos' => $this->datos_global
        ];
    }

    public function toBroadcast($notifiable) {
        return [
            'data' => [
                'datos' => $this->datos_global
            ]
        ];
    }

    public function toMail($notifiable) {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable) {
        return [
            
        ];
    }
}
