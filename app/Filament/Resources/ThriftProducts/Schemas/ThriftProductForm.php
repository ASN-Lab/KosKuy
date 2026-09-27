<?php

namespace App\Filament\Resources\ThriftProducts\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ThriftProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('seller_id')
                    ->label('Penjual')
                    ->required()
                    ->relationship('seller', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('category_id')
                    ->label('Kategori')
                    ->required()
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(4),
                TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('condition_status')
                    ->label('Kondisi Barang')
                    ->placeholder('Contoh: 90% Layak Pakai')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_sold')
                    ->label('Terjual')
                    ->default(false),
                Repeater::make('images')
                    ->label('Foto Barang')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('image_path')
                            ->image()
                            ->directory('thrift')
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Foto'),
            ]);
    }
}
