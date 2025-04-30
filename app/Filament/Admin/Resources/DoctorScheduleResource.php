<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DoctorScheduleResource\Pages;
use App\Filament\Admin\Resources\DoctorScheduleResource\RelationManagers;
use App\Models\MDoctor;
use App\Models\MPoli;
use App\Models\TDoctorPeriode;
use App\Models\TDoctorSchedule;
use Carbon\Carbon;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = TDoctorPeriode::class;
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Periode Jaga';
    protected static ?string $breadcrumb = "Periode Jaga";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            DatePicker::make('periode')->label('Periode')->required()
                ->default(now())
                ->native(false)
                ->firstDayOfWeek(7)
                ->placeholder('Pilih Periode')->required(),
            Select::make('is_active')
                ->label('Status Periode')
                ->placeholder('Pilih Status')
                ->options([
                    1 => 'Aktif',
                    0 => 'Tidak Aktif',
                ])
                ->native(false)
                ->searchable()
                ->default(1)
                ->required(),
            Section::make('Informasi Dokter')->schema([
                Repeater::make('periodeDetail')->label('Lengkapi semua informasi dokter')
                    ->relationship()
                    ->id('t_doctor_periodes_id')
                    ->schema([
                        Select::make('m_doctors_id')
                            ->label('Pilih Dokter')
                            ->relationship('doctor', 'name')
                            ->placeholder('Cari nama Dokter')
                            ->options(MDoctor::where('is_active',1)->pluck('name', 'id'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => MDoctor::where('is_active', 1)->where('name', 'like', "%{$search}%")->limit(5)->pluck('name', 'id')->toArray())
                            ->getOptionLabelUsing(fn($value): ?string => MDoctor::find($value)?->name)
                            ->required(),
                        Repeater::make('schedule')->label('Jadwal Jaga Dokter')
                            ->relationship()
                            ->schema([
                                Select::make('m_polis_id')
                                    ->label('Pilih Poli')
                                    ->relationship('polis', 'title')
                                    ->placeholder('Cari nama Poli')
                                    ->options(MPoli::all()->pluck('title', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->getSearchResultsUsing(fn(string $search): array => MPoli::where('title', 'like', "%{$search}%")->limit(5)->pluck('title', 'id')->toArray())
                                    ->getOptionLabelUsing(fn($value): ?string => MPoli::find($value)?->title),
                                DatePicker::make('doctor_schedule_dates')->label('Tanggal Waktu Jaga')
                                    ->placeholder('Pilih tanggal jaga dokter')
                                    ->native(false)
                                    ->firstDayOfWeek(7)
                                    ->closeOnDateSelection()->required(),
                                TimePicker::make('doctor_schedule_start')->label('Mulai Waktu Jaga')
                                    ->placeholder('Pilih waktu mulai jaga')
                                    ->suffix('WIB')
                                    ->seconds(false)
                                    ->native(false)->required(),
                                TimePicker::make('doctor_schedule_end')
                                    ->label('Selasai Waktu Jaga')
                                    ->placeholder('Pilih waktu selesai jaga')
                                    ->suffix('WIB')
                                    ->seconds(false)->native(false)->required()
                            ])
                            ->defaultItems(0)
                            ->dehydrated(true)
                            ->reorderableWithButtons()
                            ->reorderableWithDragAndDrop(true)
                            ->collapsible()
                            ->addActionLabel('Tambah Jadwal')
                            ->itemLabel(fn(array $state): ?string => ($state['doctor_schedule_dates'] ? Carbon::parse($state['doctor_schedule_dates'])->format('d F Y') : null) ?? null)
                    ])
                    ->defaultItems(1)
                    ->reorderable(true)
                    ->dehydrated(true)
                    ->reorderableWithButtons()
                    ->reorderableWithDragAndDrop(true)
                    ->collapsible()
                    ->addActionLabel('Tambah Informasi Dokter'),
            ])
        ]);
    }

    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('periode')->label('Periode Jaga')->formatStateUsing(function ($state) {
                    return Carbon::parse($state)->format('F Y');
                }),
                TextColumn::make('total_dokter')->label('Total Dokter')->getStateUsing(function ($record) {
                    return $record->doctors()->distinct()->count();
                }),
                TextColumn::make('is_active')->label('Status Periode')->badge()->color(fn(string $state): string => match ($state) {
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
            'index' => Pages\ListDoctorSchedules::route('/'),
            'create' => Pages\CreateDoctorSchedule::route('/create'),
            'edit' => Pages\EditDoctorSchedule::route('/{record}/edit'),
        ];
    }
}
