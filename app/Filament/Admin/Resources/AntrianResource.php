<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AntrianResource\Pages;
use App\Models\TAntrian;
use App\Models\TDoctorSchedule;
use Carbon\Carbon;
use Filament\Actions\StaticAction;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('date_treatment')
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->query(TAntrian::with('schedule_docter')->orderBy('antrian', 'asc'))
            ->columns([
                TextColumn::make('antrian')->label('Nomor Antrian')
                    ->getStateUsing(function ($record) {
                        return $record->m_statuses_id == 1 ? 'Belum Ada' : $record->antrian;
                    }),
                TextColumn::make('name')->label('Nama Pasien'),
                TextColumn::make('date_treatment')->label('Waktu Berobat')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d F Y')),
                TextColumn::make('m_polis_id')->label('Poli')
                    ->getStateUsing(fn($record) => $record->polis ? $record->polis->title : 'Tidak Ada'),
                TextColumn::make('dokter')->label('Dokter Jaga')->getStateUsing(function ($record) {
                        $find = TDoctorSchedule::where('doctor_schedule_dates', $record->date_treatment)
                            ->where('m_polis_id', $record->m_polis_id)
                                ->with('periode', function ($a) {
                                    $a->with('doctor');
                                })
                                ->first();
                        return $find->periode->doctor->name ?? 'Kosong';
                    }),
                TextColumn::make('payment')->label('Pembayaran')->badge()
                    ->getStateUsing(function ($record) {
                        return $record->payment == 0 ? 'Cash' : 'BPJS';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Cash' => 'success',
                        'BPJS' => 'danger',
                    }),
                TextColumn::make('m_statuses_id')->label('Status Berobat')->badge()->color(fn(string $state): string => match ($state) {
                    'DRAFT' => 'gray',
                    'DI AJUKAN' => 'success',
                    'SEDANG BEROBAT' => 'info',
                    'SELESAI BEROBAT' => 'warning',
                    'DI BATALKAN' => 'danger',
                    'DI TOLAK' => 'danger',
                })
                    ->getStateUsing(fn($record) => $record->status ? $record->status->title : 'Tidak Ada'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ActionGroup::make([
                Action::make('sedang_berobat')
                    ->label('Panggil Pasien')
                    ->action(function ($record) {
                    $record->update([
                            'm_statuses_id' => 3,
                        ]);
                    })
                    ->visible(fn($record) => $record->m_statuses_id === 2)
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pemanggilan Pasien')
                    ->modalDescription('Apakah Pasien sudah diperbolehkan bertemu dokter ?')
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                    DeleteAction::make()
                        ->visible(fn($record) => $record->m_statuses_id === 1)
                        ->requiresConfirmation()
                        ->action(fn(TAntrian $record) => $record->delete())
                        ->modalHeading('Hapus Data'),
                    Tables\Actions\ViewAction::make()->modalHeading('Detail Informasi Antrian')
                ])->button()
                    ->label('Aksi')->color('info')
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
