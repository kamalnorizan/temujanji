<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CheckAppointmentStatus implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Semak status temujanji berdasarkan No Temujanji';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $appointmentNo = $request['appointment_no'];

        $appointment = \App\Models\Appointment::where('appointment_no', $appointmentNo)->first();

        if (!$appointment) {
            return 'Temujanji tidak ditemui.';
        }

      return <<<TEXT
        Status Temujanji:
        No Temujanji: {$appointment->appointment_no}
        Nama: {$appointment->name}
        Tarikh: {$appointment->date}
        Masa: {$appointment->time}
        Tujuan: {$appointment->purpose}
        Ulasan: {$appointment->notes}
        Status: {$appointment->status}
        TEXT;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'appointment_no' => $schema->string()
                            ->description('No Temujanji contohnya APT-20260608-0001')
                            ->required(),
        ];
    }
}
