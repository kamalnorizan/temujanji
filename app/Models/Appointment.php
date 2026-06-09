<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use Notifiable;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function counselingRoom()
    {
        return $this->belongsTo(CounselingRoom::class, 'counseling_room_id');
    }

    public function timelines()
    {
        return $this->hasMany(AppointmentTimeline::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCanceled()
    {
        return $this->status === 'cancelled';
    }

    public function statusText(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pengesahan',
            'approved' => 'Diluluskan',
            'scheduled' => 'Dijadualkan',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'text-bg-warning ',
            'approved' => 'text-bg-success ',
            'scheduled' => 'text-bg-primary ',
            'rejected' => 'text-bg-danger ',
            'completed' => 'text-bg-secondary ',
            'cancelled' => 'text-bg-secondary ',
            default => 'text-bg-secondary ',
        };
    }
}
