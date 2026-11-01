<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('payable_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) => $state === 'App\Models\Booking' ? 'Kos' : 'Thrift'),
                TextColumn::make('amount')
                    ->label('Total')
                    ->money('IDR'),
                TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        Payment::METHOD_TRANSFER => 'Transfer',
                        Payment::METHOD_CASH => 'COD',
                        default => $state
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_PAID => 'success',
                        Payment::STATUS_PENDING_VERIFICATION, Payment::STATUS_PENDING_PICKUP => 'warning',
                        Payment::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                ImageColumn::make('proof_path')
                    ->label('Bukti')
                    ->circular(),
                TextColumn::make('created_at')
                    ->label('Tgl')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Payment::STATUS_PENDING_VERIFICATION => 'Menunggu Verifikasi',
                        Payment::STATUS_PAID => 'Lunas',
                        Payment::STATUS_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}