<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PoliResource\Pages;
use App\Filament\Admin\Resources\PoliResource\RelationManagers;
use App\Models\MDoctor;
use App\Models\MPoli;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoliResource extends Resource
{
    protected static ?string $model = MPoli::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Poli';
    protected static ?string $breadcrumb = "Poli";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->label('Nama Poli'),
                Select::make('m_doctors_id')
                    ->multiple()
                    ->label('Author')
                    ->placeholder('Cari nama dokter')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => MDoctor::where('name', 'like', "%{$search}%")->limit(5)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => MDoctor::find($value)?->name),
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
            'index' => Pages\ListPolis::route('/'),
            'create' => Pages\CreatePoli::route('/create'),
            'edit' => Pages\EditPoli::route('/{record}/edit'),
        ];
    }
}
