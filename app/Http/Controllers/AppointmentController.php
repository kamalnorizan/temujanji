<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Jobs\AppointmentNotificationJob;
use App\Models\Appointment;
use App\Notifications\AppointmentCreatedNotification;
use App\Services\UstazaiWhatsappService;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if($user->isAdmin()) {
            $appointments = Appointment::all();
        } else {
            $appointments = $user->appointments;
        }
        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        return view('appointments.create');
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = new Appointment();
        $appointment->uuid = Uuid::uuid4()->toString();
        $appointment->user_id = auth()->id();
        $currAppointmentCount = Appointment::count();
        $appointment->appointment_no = 'APPT-' . date('Ymd') . '-' . str_pad($currAppointmentCount + 1, 4, '0', STR_PAD_LEFT);
        $user = auth()->user();
        $appointment->name = $user->name;
        $appointment->email = $user->email;
        $appointment->phone = $user->phone;
        $appointment->purpose = $request->input('purpose');
        $appointment->status = 'pending';
        $appointment->save();

        // AppointmentNotificationJob::dispatch($appointment)->delay(now()->addSecond());

        $appointment->notify(new AppointmentCreatedNotification($appointment));

        return redirect()->route('appointments.index')->with('success', 'Temujanji berjaya dibuat!');
    }

    public function calendar()
    {
        $pendingAppointments = Appointment::where('status', 'pending')->get();
        return view('appointments.calendar', compact('pendingAppointments'));
    }
}
