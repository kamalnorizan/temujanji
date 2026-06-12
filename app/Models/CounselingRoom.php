<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselingRoom extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function activeText()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }
}
