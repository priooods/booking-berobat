<?php

namespace App\Filament\Admin\Resources\AntrianResource\Pages;

use App\Filament\Admin\Resources\AntrianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAntrian extends CreateRecord
{
    protected static string $resource = AntrianResource::class;
    protected ?string $heading = 'Buat Antrian Pasien';
    protected static ?string $title = 'Buat Antrian';
    protected static ?string $breadcrumb = "Create";
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['m_statuses_id'] = 1;
        $data['user_id'] = auth()->user()->id;
        $data['antrian'] = 1;
        return $data;
    }
}
