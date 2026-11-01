<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Payment;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('payable_type')
                            ->label('Tipe Transaksi')
                            ->content(fn ($record) => $record?->payable_type === 'App\Models\Booking' ? 'Booking Kos' : 'Thrift Product'),
                        Placeholder::make('payable_id')
                            ->label('ID Transaksi')
                            ->content(fn ($record) => "#" . $record?->payable_id),
                        TextInput::make('amount')
                            ->label('Total Nominal')
                            ->prefix('Rp')
                            ->numeric()
                            ->readOnly(),
                        TextInput::make('unique_code')
                            ->label('Kode Unik')
                            ->numeric()
                            ->readOnly(),
                        Select::make('method')
                            ->label('Metode Pembayaran')
                            ->options([
                                Payment::METHOD_TRANSFER => 'Transfer Bank',
                                Payment::METHOD_CASH => 'Tunai di Tempat (COD)',
                            ])
                            ->readOnly(),
                    ]),

                Section::make('Bukti Pembayaran')
                    ->schema([
                        FileUpload::make('proof_path')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('payment-proofs')
                            ->readOnly(fn ($context) => $context === 'edit'),
                    ])
                    ->visible(fn ($get) => $get('method') === Payment::METHOD_TRANSFER),

                Section::make('Verifikasi (Admin/Owner)')
                    ->schema([
                        Select::make('status')
                            ->label('Status Verifikasi')
                            ->options([
                                Payment::STATUS_UNPAID => 'Belum Bayar',
                                Payment::STATUS_PENDING_VERIFICATION => 'Menunggu Verifikasi',
                                Payment::STATUS_PENDING_PICKUP => 'Menunggu Diambil (COD)',
                                Payment::STATUS_PAID => 'Sudah Bayar / Lunas',
                                Payment::STATUS_REJECTED => 'Ditolak',
                            ])
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Catatan / Alasan Reject')
                            ->placeholder('Opsional'),
                    ]),
            ]);
    }
}