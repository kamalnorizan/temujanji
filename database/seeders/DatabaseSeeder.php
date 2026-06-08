<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentTimeline;
use App\Models\CounselingRoom;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@example.com',
                'phone'    => '0123456789',
                'role'     => 'admin',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Pegawai Psikologi 1',
                'email'    => 'pegawai1@example.com',
                'phone'    => '0123456781',
                'role'     => 'pegawai',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Pegawai Psikologi 2',
                'email'    => 'pegawai2@example.com',
                'phone'    => '0123456782',
                'role'     => 'pegawai',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Pengguna Demo 1',
                'email'    => 'user1@example.com',
                'phone'    => '0123456783',
                'role'     => 'pengguna',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Pengguna Demo 2',
                'email'    => 'user2@example.com',
                'phone'    => '0123456784',
                'role'     => 'pengguna',
                'password' => Hash::make('password'),
            ],
            [
                'name'     => 'Pengguna Demo 3',
                'email'    => 'user3@example.com',
                'phone'    => '0123456785',
                'role'     => 'pengguna',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // Create counseling rooms
        $rooms = [
            ['uuid'=>Uuid::uuid4()->toString(), 'name' => 'Bilik Kaunseling 1', 'location' => 'Aras 1', 'is_active' => true],
            ['uuid'=>Uuid::uuid4()->toString(), 'name' => 'Bilik Kaunseling 2', 'location' => 'Aras 1', 'is_active' => true],
            ['uuid'=>Uuid::uuid4()->toString(), 'name' => 'Bilik Kaunseling 3', 'location' => 'Aras 2', 'is_active' => true],
        ];

        foreach ($rooms as $data) {
            CounselingRoom::updateOrCreate(
                ['uuid' => $data['uuid']],
                $data
            );
        }

        // Get users and rooms for appointment seeding
        $user1 = User::where('email', 'user1@example.com')->first();
        $user2 = User::where('email', 'user2@example.com')->first();
        $user3 = User::where('email', 'user3@example.com')->first();
        $pegawai1 = User::where('email', 'pegawai1@example.com')->first();
        $pegawai2 = User::where('email', 'pegawai2@example.com')->first();

        $room1 = CounselingRoom::where('uuid', $rooms[0]['uuid'])->first();
        $room2 = CounselingRoom::where('uuid', $rooms[1]['uuid'])->first();
        $room3 = CounselingRoom::where('uuid', $rooms[2]['uuid'])->first();

        $today = now();

        // Create appointments with various statuses
        $appointments = [
            [
                'user_id'        => $user1->id,
                'uuid'           => Uuid::uuid4()->toString(),
                'appointment_no' => 'APT-' . $today->format('Ymd') . '-0001',
                'name'           => 'Pengguna Demo 1',
                'phone'          => '0123456783',
                'email'          => 'user1@example.com',
                'purpose'        => 'Konsultasi masalah akademik dan tekanan ujian',
                'notes'          => 'Siswa tahun 2, mengalami stress tinggi',
                'status'         => 'pending',
            ],
            [
                'user_id'        => $user2->id,
                'uuid'           => Uuid::uuid4()->toString(),
                'officer_id'     => $pegawai1->id,
                'counseling_room_id' => $room1->id,
                'appointment_no' => 'APT-' . $today->format('Ymd') . '-0002',
                'name'           => 'Pengguna Demo 2',
                'phone'          => '0123456784',
                'email'          => 'user2@example.com',
                'purpose'        => 'Soal hubungan keluarga',
                'notes'          => 'Butuh bimbingan menangani konflik dengan ibu bapa',
                'scheduled_date' => $today->addDays(2)->format('Y-m-d'),
                'start_time'     => '10:00:00',
                'end_time'       => '11:00:00',
                'status'         => 'scheduled',
            ],
            [
                'user_id'        => $user3->id,
                'uuid'           => Uuid::uuid4()->toString(),
                'officer_id'     => $pegawai2->id,
                'counseling_room_id' => $room2->id,
                'appointment_no' => 'APT-' . $today->format('Ymd') . '-0003',
                'name'           => 'Pengguna Demo 3',
                'phone'          => '0123456785',
                'email'          => 'user3@example.com',
                'purpose'        => 'Konsultasi perkembangan diri',
                'notes'          => 'Mencari arah dan tujuan hidup',
                'scheduled_date' => $today->addDays(3)->format('Y-m-d'),
                'start_time'     => '14:00:00',
                'end_time'       => '15:00:00',
                'status'         => 'approved',
            ],
        ];

        foreach ($appointments as $data) {
            $apt = Appointment::updateOrCreate(
                ['appointment_no' => $data['appointment_no']],
                $data
            );

            // Create timeline entries
            if ($apt->status === 'pending') {
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Permohonan Dihantar'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->user_id,
                        'title' => 'Permohonan Dihantar',
                        'description' => 'Permohonan appointment telah dihantar oleh pengguna',
                        'status' => 'pending',
                        'created_at' => now(),
                    ]
                );
            } elseif ($apt->status === 'scheduled') {
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Permohonan Dihantar'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->user_id,
                        'title' => 'Permohonan Dihantar',
                        'description' => 'Permohonan appointment telah dihantar oleh pengguna',
                        'status' => 'pending',
                        'created_at' => now()->subHours(2),
                    ]
                );
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Jadual Ditetapkan'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->officer_id,
                        'title' => 'Jadual Ditetapkan',
                        'description' => 'Appointment telah dijadualkan oleh ' . $apt->officer->name,
                        'status' => 'scheduled',
                        'created_at' => now(),
                    ]
                );
            } elseif ($apt->status === 'approved') {
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Permohonan Dihantar'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->user_id,
                        'title' => 'Permohonan Dihantar',
                        'description' => 'Permohonan appointment telah dihantar oleh pengguna',
                        'status' => 'pending',
                        'created_at' => now()->subHours(4),
                    ]
                );
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Jadual Ditetapkan'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->officer_id,
                        'title' => 'Jadual Ditetapkan',
                        'description' => 'Appointment telah dijadualkan oleh ' . $apt->officer->name,
                        'status' => 'scheduled',
                        'created_at' => now()->subHours(2),
                    ]
                );
                AppointmentTimeline::updateOrCreate(
                    ['appointment_id' => $apt->id, 'title' => 'Disahkan'],
                    [
                        'uuid' => Uuid::uuid4()->toString(),
                        'user_id' => $apt->officer_id,
                        'title' => 'Disahkan',
                        'description' => 'Appointment telah disahkan oleh ' . $apt->officer->name,
                        'status' => 'approved',
                        'created_at' => now(),
                    ]
                );
            }
        }

    }
}
