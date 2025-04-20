<?php

namespace App\Filament\Admin\Resources\PoliResource\Pages;

use App\Filament\Admin\Resources\PoliResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoli extends EditRecord
{
    protected static string $resource = PoliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
