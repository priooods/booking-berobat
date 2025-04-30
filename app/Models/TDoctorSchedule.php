<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDoctorSchedule extends Model
{
    protected $fillable = [
        't_doctor_periode_details_id',
        'm_polis_id',
        'doctor_schedule_start',
        'doctor_schedule_end',
        'doctor_schedule_dates',
    ];

    public function periode(){
        return $this->belongsTo(TDoctorPeriodeDetail::class, 't_doctor_periode_details_id');
    }

    public function polis()
    {
        return $this->belongsTo(MPoli::class, 'm_polis_id');
    }

}
