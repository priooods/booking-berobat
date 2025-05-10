<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DoctorResource\Pages;
use App\Models\MDoctor;
use App\Models\MPoli;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = MDoctor::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Dokter';
    protected static ?string $breadcrumb = "Dokter";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        if (isset(auth()->user()->role))
            if (auth()->user()->role === 1) return false;
            else return true;
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('name')->label('Nama Dokter')->placeholder('Masukan nama dokter')->required(),
            Select::make('is_active')
                ->label('Status Dokter')
                ->placeholder('Pilih Status')
                ->options([
                    1 => 'Aktif',
                    0 => 'Tidak Aktif',
                ])
                ->native(false)
                ->searchable()
                ->default(1)
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')->label('Nama Dokter'),
            TextColumn::make('is_active')->label('Status Dokter')->badge()->color(fn(string $state): string => match ($state) {
                '0' => 'danger',
                '1' => 'success',
            })->formatStateUsing(function ($state) {
                return $state == 1 ? 'Aktif' : 'Tidak Aktif';
            }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
