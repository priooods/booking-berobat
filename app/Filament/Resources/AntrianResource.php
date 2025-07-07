<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianResource\Pages;
use App\Models\MPoli;
use App\Models\TAntrian;
use App\Models\TReviewTab;
use Carbon\Carbon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lartisan\RatingTool\Forms\Components\RatingInput;
use Lartisan\RatingTool\Tables\Columns\RatingColumn;

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
            Select::make('blode')
                ->label('Gol Darah')->required()
                ->placeholder('Pilih Gol Darah')
                ->options([
                    'A' => 'A',
                    'B' => 'B',
                    'AB' => 'AB',
                    'O' => 'O',
                    'Tidak Tahu' => 'Tidak Tahu',
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

    public static function table(Table $table): Table
    {
        return $table
            ->query(TAntrian::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc'))
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
            TextColumn::make('diagnosa')->label('Keluhan Sakit'),
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
            RatingColumn::make('myrating')
                ->size('xs')
                ->label('Rating')
                ->icon('heroicon-s-star')
                ->getStateUsing(fn($record) => $record->myrating ? $record->myrating->start : 0)
                ->color('warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ActionGroup::make([
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
                Action::make('ratings')
                    ->label('Beri Rating')
                    ->form([
                        RatingInput::make('rating')->maxValue(5)->size('xl')->icon('heroicon-o-star')->color('warning'),
                        Textarea::make('description')->label('Deskripsi')->placeholder('Masukan Penilaian anda')
                    ])
                    ->action(function (array $data, TAntrian $record) {
                        if (!isset($data['description']))
                            return;
                        else {
                            TReviewTab::create([
                                'users_id' => auth()->user()->id,
                                'start' => $data['rating'],
                                'description' => $data['description'],
                                't_antrian_tabs_id' => $record->id,
                            ]);
                        }
                    })
                    ->visible(fn($record) => $record->m_statuses_id === 4 && !isset($record->myrating->start))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->modalWidth(MaxWidth::Small)
                    ->modalHeading('Rating untuk Pelayanan Puskesmas')
                    ->modalSubmitActionLabel('Beri Rating')
                    ->modalCancelAction(fn(StaticAction $action) => $action->label('Batal')),
                Tables\Actions\EditAction::make()->visible(fn($record) => $record->m_statuses_id === 1),
                Tables\Actions\ViewAction::make()
                    ->form([
                        Section::make('Antrian Anda')->columns(2)->schema([
                            Group::make([
                                TextInput::make('antrian')->label('Antrian Saat Ini')->readOnly()
                            ])->relationship('antrian_now'),
                            TextInput::make('antrian')->label('Nomor Antrian Anda')->readOnly(),
                            Group::make([
                                Group::make([
                                    Group::make([
                                        TextInput::make('name')->label('Dokter Jaga')->readOnly()
                                    ])->relationship('doctor')
                                ])->relationship('periode')
                            ])->relationship('mydoctor'),
                            Group::make([
                                TextInput::make('title')->label('Status Antrian')->readOnly()
                            ])->relationship('status')
                        ]),
                        Section::make('Informasi Pasien')->columns(2)->schema([
                            TextInput::make('number_ktp')->label('Nomor KTP')->readOnly(),
                            TextInput::make('name')->label('Nama Pasien')->readOnly(),
                            Select::make('gender')
                                ->label('Jenis Kelamin')->required()
                                ->placeholder('Pilih Jenis Kelamin')
                                ->options([
                                    0 => 'Wanita',
                                    1 => 'Pria'
                                ]),
                            TextInput::make('birthday')->label('Tgl lahir Pasien')->readOnly(),
                            TextInput::make('phone')->label('No. Handphone')->readOnly(),
                            Textarea::make('address')->label('Alamat')->readOnly(),
                        ]),
                        Section::make('Informasi Berobat')->columns(2)->schema([
                            Textarea::make('diagnosa')->label('Keluhan Sakit')->readOnly(),
                            DatePicker::make('date_treatment')
                                ->label('Tanggal & Jam Berobat')
                                ->placeholder('Masukan Tanggal & Jam Berobat')
                                ->native(false),
                            Select::make('m_polis_id')
                                ->label('Pilih Poli')
                                ->relationship('polis', 'title')
                                ->placeholder('Cari nama Poli')
                                ->options(MPoli::all()->pluck('title', 'id'))
                                ->searchable()
                                ->getSearchResultsUsing(fn(string $search): array => MPoli::where('title', 'like', "%{$search}%")->limit(5)->pluck('title', 'id')->toArray())
                                ->getOptionLabelUsing(fn($value): ?string => MPoli::find($value)?->title),
                            Select::make('payment')
                                ->label('Jenis Pembayaran')
                                ->placeholder('Pilih Jenis Pembayaran')
                                ->options([
                                    '0' => 'Cash',
                                    '1' => 'BPJS'
                                ])
                        ])
                    ])
                    ->visible(fn($record) => $record->m_statuses_id !== 1)
                    ->modalHeading('Detail Informasi Antrian'),
                DeleteAction::make()
                    ->visible(fn($record) => $record->m_statuses_id === 1)
                    ->requiresConfirmation()
                    ->action(fn(TAntrian $record) => $record->delete())
                    ->modalHeading('Hapus Data')
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
