<?php

namespace App\Notifications;

use App\Models\Space;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SpaceCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Space $space
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nuevo Auditorio Creado: {$this->space->name}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Se ha creado un nuevo auditorio en el sistema.")
            ->line("**Nombre:** {$this->space->name}")
            ->line("**Tipo:** {$this->space->type}")
            ->line("**Capacidad:** {$this->space->capacity} personas")
            ->line("**Descripción:** {$this->space->description}")
            ->action('Ver Auditorio', url('/admin/spaces'))
            ->salutation('Saludos, el sistema de gestión de auditorios.');
    }
}
