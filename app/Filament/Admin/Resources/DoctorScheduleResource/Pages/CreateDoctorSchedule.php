<?php

namespace App\Filament\Admin\Resources\DoctorScheduleResource\Pages;

use App\Filament\Admin\Resources\DoctorScheduleResource;
use App\Models\TDoctorSchedule;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;
    protected ?string $heading = 'Tambah Periode Jaga';
    protected static ?string $title = 'Tambah Periode';
    
}
