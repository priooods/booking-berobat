<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TAntrian extends Model
{
    protected $fillable = [
        'antrian',
        'number_ktp',
        'name',
        'user_id',
        'm_polis_id',
        'gender',
        'birthday',
        'phone',
        'address',
        'diagnosa',
        'date_treatment',
        'payment',
        'm_statuses_id',
        'no_bpjs'
    ];

    public function mydoctor()
    {
        return $this->hasOne(TDoctorSchedule::class, 'doctor_schedule_dates', 'date_treatment')->where('m_polis_id', $this->m_polis_id);
    }

    public function antrian_now()
    {
        return $this->hasOne(TAntrian::class, 'date_treatment', 'date_treatment')->where('m_statuses_id', 3)->orderBy('antrian', 'desc');
    }

    public function schedule_docter()
    {
        return $this->hasOne(TDoctorSchedule::class, 'doctor_schedule_dates', 'date_treatment');
    }

    public function polis()
    {
        return $this->belongsTo(MPoli::class, 'm_polis_id');
    }

    public function status()
    {
        return $this->belongsTo(MStatus::class, 'm_statuses_id');
    }
}
