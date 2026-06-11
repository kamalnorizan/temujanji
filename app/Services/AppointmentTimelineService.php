<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentTimeline;

class AppointmentTimelineService
{
    public function create(Appointment $appointment, string $title, ?string $description = null, ?string $status= null, ?int $userId = null): AppointmentTimeline {

        if(!$userId && auth()->check()) {
            $userId = auth()->id();
        }

        $timeline = new AppointmentTimeline();
        $timeline->uuid = \Illuminate\Support\Str::uuid()->toString();
        $timeline->appointment_id = $appointment->id;
        $timeline->title = $title;
        $timeline->description = $description;
        $timeline->status = $status;
        $timeline->user_id = $userId;
        $timeline->save();

        return $timeline;
    }
}
