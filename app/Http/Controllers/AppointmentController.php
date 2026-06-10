<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Jobs\AppointmentNotificationJob;
use App\Models\Appointment;
use App\Models\CounselingRoom;
use App\Notifications\AppointmentCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            $appointments = Appointment::all();
        } else {
            $appointments = $user->appointments;
        }

        Cache::remember('appointments', now()->addMinutes(10), function () {
            return Appointment::all();
        });

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        return view('appointments.create');
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = new Appointment;
        $appointment->uuid = Uuid::uuid4()->toString();
        $appointment->user_id = auth()->id();
        $currAppointmentCount = Appointment::count();
        $appointment->appointment_no = 'APPT-'.date('Ymd').'-'.str_pad($currAppointmentCount + 1, 4, '0', STR_PAD_LEFT);
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

    public function update(UpdateAppointmentRequest $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->name = $request->input('name');
        $appointment->phone = $request->input('phone');
        $appointment->email = $request->input('email');
        $appointment->purpose = $request->input('purpose');
        $appointment->notes = $request->input('notes');
        $appointment->counseling_room_id = $request->input('counseling_room_id');
        $appointment->scheduled_date = $request->input('scheduled_date');
        $appointment->start_time = Carbon::createFromFormat('H:i:s', $request->input('start_time'))
            ->format('H:i:s');
        $appointment->end_time = Carbon::createFromFormat('H:i:s', $request->input('start_time'))
            ->addHour()
            ->format('H:i:s');
        $appointment->status = 'scheduled';
        $appointment->officer_id = auth()->id();
        $appointment->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Temujanji berjaya disimpan!',
                'appointment' => $appointment->fresh(),
            ]);
        }

        return redirect()->route('appointments.calendar')->with('success', 'Temujanji berjaya disimpan!');
    }

    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        $counseling_rooms = CounselingRoom::pluck('name', 'id');

        if (request()->ajax()) {
            return response()->json([
                'appointment' => $appointment,
                'counseling_rooms' => $counseling_rooms,
            ]);
        }

        return view('appointments.show', compact('appointment', 'counseling_rooms'));
    }

    public function availableTimeCheck(Request $request)
    {
        $date = $request->input('date');
        $counseling_room_id = $request->input('counseling_room_id');

        $appointments = Appointment::whereDate('scheduled_date', $date)
            ->where('status', 'scheduled')
            ->where('counseling_room_id', $counseling_room_id)
            ->get();

        $times = [
            '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00',
            '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00',
        ];

        $takenTimes = $appointments->pluck('start_time')->toArray();

        $availableTimes = collect($times)->map(function (string $time) use ($takenTimes) {
            return [
                'time' => $time,
                'available' => ! in_array($time, $takenTimes, true),
            ];
        });

        return response()->json($availableTimes);
    }

    public function calendar()
    {
        $appointments = Cache::get('appointments', function () {
            $appointments = Appointment::all()->toArray();
            Cache::put('appointments', $appointments, now()->addDay());
            return $appointments;
        });

        dd($appointments);
        $pendingAppointments = Appointment::where('status', 'pending')->get();
        $calendarEvents = Appointment::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_date')
            ->whereNotNull('start_time')
            ->get()
            ->map(function (Appointment $appointment) {
                $start = Carbon::parse($appointment->scheduled_date.' '.$appointment->start_time);
                $end = $appointment->end_time
                    ? Carbon::parse($appointment->scheduled_date.' '.$appointment->end_time)
                    : (clone $start)->addHour();


                return [
                    'id' => $appointment->id,
                    'title' => $appointment->purpose,
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'backgroundColor' => '#0d6efd',
                    'borderColor' => '#0d6efd',
                    'extendedProps' => [
                        'name' => $appointment->name,
                        'phone' => $appointment->phone,
                        'email' => $appointment->email,
                        'notes' => $appointment->notes,
                        'counseling_room_id' => $appointment->counseling_room_id,
                    ],
                ];
            })
            ->values();

        return view('appointments.calendar', compact('pendingAppointments', 'calendarEvents'));
    }
}
