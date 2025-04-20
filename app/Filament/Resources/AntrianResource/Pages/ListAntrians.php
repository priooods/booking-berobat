<?php

namespace App\Filament\Resources\AntrianResource\Pages;

use App\Filament\Resources\AntrianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAntrians extends ListRecords
{
    protected static string $resource = AntrianResource::class;
    protected static ?string $title = 'Antrian';
    protected ?string $heading = 'Data Antrian Pasien';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
