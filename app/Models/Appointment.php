<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
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
}
