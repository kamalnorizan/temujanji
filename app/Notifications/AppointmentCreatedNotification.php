<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public int $tries = 3;
    public int $timeout = 120;

    public Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Temujanji berjaya dicipta: ' . $this->appointment->appointment_no)
            ->greeting('Salam, ' . $this->appointment->name)
            ->line('Kami telah menerima temujanji anda untuk tujuan: ' . $this->appointment->purpose)
            ->line('Anda akan menerima maklumbalas tarikh dan masa temujanji melalui WhatsApp.')
            ->action('Semak status temujanji anda', url('/appointments/' . $this->appointment->uuid))
            ->line('Terima kasih kerana menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_no' => $this->appointment->appointment_no,
            'name' => $this->appointment->name,
            'purpose' => $this->appointment->purpose,
            'uuid' => $this->appointment->uuid
        ];
    }
}
