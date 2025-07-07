<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TReviewTab extends Model
{
    protected $fillable = [
        'users_id',
        'start',
        'description',
        't_antrian_tabs_id'
    ];
}
