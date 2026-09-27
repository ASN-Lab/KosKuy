<?php

namespace App\Filament\Resources\ThriftProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ThriftProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('title')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('seller.name')
                    ->label('Penjual')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatMoney('IDR', symbol: false, decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('condition_status')
                    ->label('Kondisi'),
                IconColumn::make('is_sold')
                    ->label('Terjual')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_sold')
                    ->label('Status Terjual'),
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
