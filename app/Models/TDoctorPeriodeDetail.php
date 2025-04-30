<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDoctorPeriodeDetail extends Model
{
    public $timestamps = false;
    protected $fillable = [
        't_doctor_periodes_id',
        'm_doctors_id'
    ];

    public function schedule()
    {
        return $this->hasMany(TDoctorSchedule::class, 't_doctor_periode_details_id', 'id');
    }

    public function periode()
    {
        return $this->belongsTo(TDoctorPeriode::class, 't_doctor_periodes_id');
    }

    public function doctor()
    {
        return $this->belongsTo(MDoctor::class, 'm_doctors_id');
    }
}
