<?php

namespace App\Filament\Resources\AntrianResource\Pages;

use App\Filament\Resources\AntrianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAntrian extends EditRecord
{
    protected static string $resource = AntrianResource::class;
    protected static ?string $breadcrumb = "Edit";
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
