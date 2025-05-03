<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;
use Filament\Pages\Page;

class Beranda extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.beranda';
    public function getHomeUrl(): string
    {
        return Dashboard::getUrl(); // arahkan ke halaman custom yang kamu buat
    }

    public function getTitle(): string
    {
        return '';
    }
}
