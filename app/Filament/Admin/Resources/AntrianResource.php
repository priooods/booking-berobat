<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AntrianResource\Pages;
use App\Models\MPoli;
use App\Models\TAntrian;
use App\Models\TDoctorSchedule;
use Carbon\Carbon;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

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
            TextInput::make('number_ktp')->label('No KTP')->placeholder('Masukan No KTP')->numeric()->required(),
            TextInput::make('name')->label('Nama Pasien')->placeholder('Masukan Nama Lengkap Pasien')->required(),
            DatePicker::make('birthday')
                ->label('Tanggal Lahir')
                ->placeholder('Masukan Tanggal Lahir')->required()
                ->native(false),
            Select::make('gender')
                ->label('Jenis Kelamin')->required()
                ->placeholder('Pilih Jenis Kelamin')
                ->options([
                    0 => 'Wanita',
                    1 => 'Pria'
                ]),
            TextInput::make('phone')
                ->label('No Handphone')
                ->placeholder('Gunakan awalan 08')
                ->numeric()->required(),
            Textarea::make('address')->label('Alamat')->placeholder('Masukan Alamat Lengkap')->autosize()->required(),
            Section::make('Detail Berobat')->schema([
                Textarea::make('diagnosa')->label('Detail Keluhan Sakit')->placeholder('Masukan Keluhan Sakit')->autosize()->required(),
                Select::make('m_polis_id')
                    ->label('Pilih Poli')
                    ->relationship('polis', 'title')
                    ->placeholder('Cari nama Poli')
                    ->options(MPoli::all()->pluck('title', 'id'))
                    ->searchable()
                    ->required()
                    ->getSearchResultsUsing(fn(string $search): array => MPoli::where('title', 'like', "%{$search}%")->limit(5)->pluck('title', 'id')->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => MPoli::find($value)?->title),
                DatePicker::make('date_treatment')
                    ->label('Tanggal & Jam Berobat')
                    ->placeholder('Masukan Tanggal & Jam Berobat')
                    ->native(false)->required(),
                Select::make('payment')
                    ->label('Jenis Pembayaran')
                    ->placeholder('Pilih Jenis Pembayaran')
                    ->options([
                        '0' => 'Cash',
                        '1' => 'BPJS'
                    ])
                    ->required()
                    ->live(onBlur: true),
                TextInput::make('no_bpjs')
                    ->numeric()
                    ->label('No BPJS')
                    ->placeholder('Masukan No BPJS')
                    ->visible(fn($get) => $get('payment') === '1')
                    ->required(fn($get) => $get('payment') === '1')
            ])
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
            ->query(
                function () {
                    if (auth()->user()->role == 2) {
                        return TAntrian::with('schedule_docter')
                            ->orderBy('date_treatment', 'asc')
                            ->orderBy('m_statuses_id', 'asc')
                            ->orderBy('antrian', 'asc');
                    } else if (auth()->user()->role == 1) {
                        return TAntrian::with('schedule_docter')
                            ->whereIn('m_statuses_id', [1, 2])
                            ->orderBy('date_treatment', 'asc')
                            ->orderBy('m_statuses_id', 'asc')
                            ->orderBy('antrian', 'asc');
                    } else {
                        return TAntrian::with('schedule_docter')
                            ->where('m_statuses_id', 3)
                            ->orderBy('date_treatment', 'asc')
                            ->orderBy('m_statuses_id', 'asc')
                            ->orderBy('antrian', 'asc');
                    }
                }
            )
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
            Filter::make('date_treatment')
                ->form([
                    DatePicker::make('start_date')->label('Dari Tanggal'),
                    DatePicker::make('start_end')->label('Sampai Tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['start_date'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_treatment', '>=', $date),
                        )
                        ->when(
                            $data['start_end'],
                            fn(Builder $query, $date): Builder => $query->whereDate('date_treatment', '<=', $date),
                        );
                })
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
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
                Action::make('selesai_berobat')
                    ->label('Selesai Berobat')
                    ->action(function ($record) {
                        $record->update([
                            'm_statuses_id' => 4,
                        ]);
                    })
                    ->visible(fn($record) => $record->m_statuses_id === 3 && auth()->user()->role === 3)
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Selesai Berobat')
                    ->modalDescription('Apakah Pasien sudah Selesai Berobat ?')
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->action(function ($record) {
                        $find = TAntrian::where('m_statuses_id', '!=', 1)
                            ->where('date_treatment', $record['date_treatment'])
                            ->orderBy('antrian', 'desc')
                            ->first();
                        if (isset($find)) {
                            $record->update([
                                'm_statuses_id' => 2,
                                'antrian' => ((int)$find['antrian'] + 1),
                            ]);
                        } else {
                            $record->update([
                                'm_statuses_id' => 2,
                            ]);
                        }
                    })
                    ->visible(fn($record) => $record->m_statuses_id === 1)
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengajuan Berobat')
                    ->modalDescription('Apakah anda yakin ingin mengirim pengajuan berobat ?')
                    ->modalSubmitActionLabel('Konfirmasi')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                DeleteAction::make()
                    ->visible(fn($record) => $record->m_statuses_id === 1)
                    ->requiresConfirmation()
                    ->action(fn(TAntrian $record) => $record->delete())
                    ->modalHeading('Hapus Data'),
                Tables\Actions\ViewAction::make()->modalHeading('Detail Informasi Antrian')
            ])->button()->label('Aksi')->color('info')
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
