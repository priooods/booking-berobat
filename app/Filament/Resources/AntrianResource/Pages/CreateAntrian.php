<?php

namespace App\Filament\Resources\AntrianResource\Pages;

use App\Filament\Resources\AntrianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAntrian extends CreateRecord
{
    protected static string $resource = AntrianResource::class;
    protected ?string $heading = 'Buat Antrian Pasien';
    protected static ?string $title = 'Buat Antrian';
    protected static ?string $breadcrumb = "Create";
}
