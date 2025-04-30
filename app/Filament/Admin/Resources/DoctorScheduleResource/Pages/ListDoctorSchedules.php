<?php

namespace App\Filament\Admin\Resources\DoctorScheduleResource\Pages;

use App\Filament\Admin\Resources\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctorSchedules extends ListRecords
{
    protected static string $resource = DoctorScheduleResource::class;
    protected static ?string $title = 'Periode Jaga';
    protected ?string $heading = 'Data Periode Jaga';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Periode'),
        ];
    }
}
