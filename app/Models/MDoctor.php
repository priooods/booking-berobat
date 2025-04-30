<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDoctor extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];

    public function schedules()
    {
        return $this->hasMany(TDoctorSchedule::class, 'm_doctors_id');
    }
}
