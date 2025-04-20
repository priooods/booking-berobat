<?php

namespace App\Filament\Admin\Resources\PoliResource\Pages;

use App\Filament\Admin\Resources\PoliResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePoli extends CreateRecord
{
    protected static string $resource = PoliResource::class;
    protected ?string $heading = 'Tambah Data Poli';
    protected static ?string $title = 'Tambah Poli';
}
