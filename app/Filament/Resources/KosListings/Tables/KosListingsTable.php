<?php

namespace App\Filament\Resources\KosListings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KosListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('owner.name')
                    ->label('Pemilik')
                    ->searchable(),
                TextColumn::make('price_per_month')
                    ->label('Harga/Bulan')
                    ->formatMoney('IDR', symbol: false, decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('gender_type')
                    ->label('Gender')
                    ->badge(),
                IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Daftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('gender_type')
                    ->label('Tipe Gender')
                    ->options([
                        'putra' => 'Putra',
                        'putri' => 'Putri',
                        'campur' => 'Campur',
                    ]),
                TernaryFilter::make('is_available')
                    ->label('Status Ketersediaan'),
                Filter::make('unavailable')
                    ->label('Hanya Tidak Tersedia')
                    ->query(fn ($query) => $query->where('is_available', false)),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->label('Hapus (Moderasi)'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
