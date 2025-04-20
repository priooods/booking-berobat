<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianResource\Pages;
use App\Filament\Resources\AntrianResource\RelationManagers;
use App\Models\TAntrian;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AntrianResource extends Resource
{
    protected static ?string $model = TAntrian::class;
    protected static ?string $navigationLabel = 'Antrian';
    protected static ?string $breadcrumb = "Antrian";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number_ktp')->label('No KTP')->numeric(),
                TextInput::make('name')->label('Nama Pasien'),
                DatePicker::make('birthday')
                    ->label('Tanggal Lahir')
                    ->format('d/m/Y')
                    ->placeholder('MM/DD/YYYY'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        0 => 'Wanita',
                        1 => 'Pria'
                    ]),
                TextInput::make('phone')
                    ->label('No Handphone')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                Textarea::make('address')->label('Alamat')->autosize(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListAntrians::route('/'),
            'create' => Pages\CreateAntrian::route('/create'),
            'edit' => Pages\EditAntrian::route('/{record}/edit'),
        ];
    }
}
