<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\UstazaiWhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AppointmentNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    protected Appointment $appointment;
    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $app)
    {
        $this->appointment = $app;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $appointment = $this->appointment;

        $message = "Salam, ".$appointment->name."!\r\n\r\n Temujanji baru telah dibuat dengan nombor: {$appointment->appointment_no} untuk tujuan: {$appointment->purpose}. Anda akan menerima maklumbalas tarikh dan masa temujanji melalui WhatsApp.\r\n\r\nTerima kasih!";

        $whatsappService = new UstazaiWhatsappService();
        $whatsappService->sendMessageViaHttp($appointment->phone, $message);
    }
}
