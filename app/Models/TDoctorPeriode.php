<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TDoctorPeriode extends Model
{
    protected $fillable = [
        'periode',
        'is_active'
    ];

    public function periodeDetail()
    {
        return $this->hasMany(TDoctorPeriodeDetail::class, 't_doctor_periodes_id','id');
    }

    public function doctors()
    {
        return $this->hasManyThrough(MDoctor::class, TDoctorPeriodeDetail::class, 't_doctor_periodes_id', 'id', 'id', 'm_doctors_id');
    }
}
