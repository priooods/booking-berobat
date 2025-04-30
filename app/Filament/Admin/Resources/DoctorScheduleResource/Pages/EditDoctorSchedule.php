<?php

namespace App\Filament\Admin\Resources\DoctorScheduleResource\Pages;

use App\Filament\Admin\Resources\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorSchedule extends EditRecord
{
    protected static string $resource = DoctorScheduleResource::class;
    protected ?string $heading = 'Ubah Periode Jaga';
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
