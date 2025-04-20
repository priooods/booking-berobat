<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDoctor extends Model
{
    protected $fillable = [
        'name',
        'doctor_schedule',
    ];
}
