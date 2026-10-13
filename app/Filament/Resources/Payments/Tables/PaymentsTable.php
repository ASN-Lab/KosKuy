<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Penghuni')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kosListing.title')
                    ->label('Kos')
                    ->searchable()
                    ->limit(24),
                TextColumn::make('period')
                    ->label('Periode')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'paid' ? 'Lunas' : 'Belum bayar')
                    ->color(fn (string $state) => $state === 'paid' ? 'success' : 'warning'),
                TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('period', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
