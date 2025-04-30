<?php

namespace App\Filament\Admin\Resources\PoliResource\Pages;

use App\Filament\Admin\Resources\PoliResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoli extends EditRecord
{
    protected static string $resource = PoliResource::class;
    protected ?string $heading = 'Ubah Data Poli';
    protected static ?string $title = 'Ubah Poli';
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->modalHeading('Hapus Data'),
        ];
    }
}
