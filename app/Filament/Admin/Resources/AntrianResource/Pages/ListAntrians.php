<?php

namespace App\Filament\Admin\Resources\AntrianResource\Pages;

use App\Filament\Admin\Resources\AntrianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAntrians extends ListRecords
{
    protected static string $resource = AntrianResource::class;
    protected static ?string $title = 'Antrian & Pasien';
    protected ?string $heading = 'Data Antrian & Pasien';
    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
