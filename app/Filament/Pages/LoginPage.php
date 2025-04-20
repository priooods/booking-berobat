<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class LoginPage extends Login
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.login-page';
    public function getHeading(): string|Htmlable
    {
        return __('Sign In');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    // public function registerAction(): Action
    // {
    //     return Action::make('register')
    //         ->link()
    //         ->label(__('filament-panels::pages/auth/login.actions.register.label'))
    //         ->url(filament()->getRegistrationUrl());
    // }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Masuk')
            ->submit('authenticate');
    }
}
