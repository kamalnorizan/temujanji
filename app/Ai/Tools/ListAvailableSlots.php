<?php

namespace App\Ai\Tools;

use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListAvailableSlots implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
         return 'Senaraikan slot temujanji yang masih kosong berdasarkan tarikh dan no bilik kaunseling';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $date = $request['date'];
        $roomNo = $request['room_no'];

        $all_slots = collect([
            ['start_time' => Carbon::parse('08:00')],
            ['start_time' => Carbon::parse('09:00')],
            ['start_time' => Carbon::parse('10:00')],
            ['start_time' => Carbon::parse('11:00')],
            ['start_time' => Carbon::parse('12:00')],
            ['start_time' => Carbon::parse('13:00')],
            ['start_time' => Carbon::parse('14:00')],
            ['start_time' => Carbon::parse('15:00')],
            ['start_time' => Carbon::parse('16:00')],
        ]);

        $bilikKaunseling = \App\Models\CounselingRoom::where('name', $roomNo)->first();

        if (!$bilikKaunseling) {
            return "Bilik kaunseling dengan nama {$roomNo} tidak ditemui. Sila pastikan nama bilik kaunseling benar. Contohnya: Bilik Kaunseling 1, Bilik Kaunseling 2, dll.";
        }

        $bookedSlots = \App\Models\Appointment::whereDate('scheduled_date', $date)
            ->where('counseling_room_id', $bilikKaunseling->id)
            ->whereIn('status', ['scheduled', 'approved'])
            ->get(['start_time'])
            ->map(function ($appointment) {
                return [
                    'start_time' => Carbon::parse($appointment->start_time),
                ];
            });

        $availableSlots = $all_slots->reject(function ($slot) use ($bookedSlots) {
            return $bookedSlots->contains(function ($bookedSlot) use ($slot) {
                return $bookedSlot['start_time']->eq($slot['start_time']);
            });
        })->values();

        if ($availableSlots->isEmpty()) {
            return "Tiada slot temujanji tersedia untuk tarikh {$date} di bilik kaunseling {$roomNo}.";
        }

        $slots = $availableSlots->map(function ($slot) {
            return "- {$slot['start_time']->format('H:i')}";
        })->implode("\n");

        return <<<TEXT
Slot kosong untuk tarikh {$date} di bilik kaunseling {$roomNo}:

{$slots}
TEXT;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'date' => $schema->string()->description('Tarikh temujanji contohnya 2024-06-08')->required(),
            'room_no' => $schema->string()->description('No bilik kaunseling contohnya Bilik Kaunseling 2')->required(),
        ];
    }
}
