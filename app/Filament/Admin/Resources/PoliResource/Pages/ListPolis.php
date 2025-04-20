<?php

namespace App\Filament\Admin\Resources\PoliResource\Pages;

use App\Filament\Admin\Resources\PoliResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolis extends ListRecords
{
    protected static string $resource = PoliResource::class;
    protected static ?string $title = 'Poli';
    protected ?string $heading = 'Data Poli';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
