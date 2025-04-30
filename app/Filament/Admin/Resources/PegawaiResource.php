<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PegawaiResource\Pages;
use App\Filament\Admin\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                function (Page $livewire) {
                    $fields = [
                        TextInput::make('name')->label('Nama Pegawai')->placeholder('Masukan nama Pegawai')->required(),
                        TextInput::make('email')->label('Email Akun')->placeholder('Masukan Email')->required()
                    ];

                    if ($livewire instanceof CreateRecord) {
                        $fields[] = TextInput::make('password')->label('Password Akun')
                            ->password()->revealable()
                            ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                            ->same('passwordConfirmation')
                            ->placeholder('Masukan Password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required()
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $component->state('');
                            });
                        $fields[] = TextInput::make('passwordConfirmation')->label('Confirmasi Password Akun')->password()->revealable()->placeholder('Masukan Password')->required();
                    }

                    return $fields;
                }
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')->label('Nama Pegawai'),
            TextColumn::make('email')->label('Email Pegawai'),
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
