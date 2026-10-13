<?php

namespace App\Filament\Resources\KosRooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KosRoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kosListing.title')
                    ->label('Kos')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('room_number')
                    ->label('Kamar')
                    ->badge()
                    ->searchable(),
                TextColumn::make('price_per_month')
                    ->label('Harga/Bulan')
                    ->money('IDR', decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn ($record) => $record->isOccupied() ? 'Terisi' : 'Kosong')
                    ->color(fn (string $state) => $state === 'Terisi' ? 'success' : 'gray'),
            ])
            ->defaultSort('kos_listing_id')
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
