<?php

namespace App\Filament\Resources\KosListings\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KosListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('owner_id')
                    ->label('Pemilik')
                    ->required()
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->modifyQueryUsing(fn ($query) => $query->where('role', 'owner')),
                TextInput::make('title')
                    ->label('Judul Kos')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(4),
                TextInput::make('price_per_month')
                    ->label('Harga / Bulan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Select::make('gender_type')
                    ->label('Tipe Gender')
                    ->required()
                    ->options([
                        'putra' => 'Putra',
                        'putri' => 'Putri',
                        'campur' => 'Campur',
                    ]),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->decimalPlaces(8),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->decimalPlaces(8),
                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(2),
                Toggle::make('is_available')
                    ->label('Tersedia')
                    ->default(true),
                Repeater::make('facilities')
                    ->label('Fasilitas')
                    ->relationship('facilities')
                    ->schema([
                        TextInput::make('facility_name')->label('Nama Fasilitas')->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Fasilitas'),
                Repeater::make('images')
                    ->label('Foto Kos')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('image_path')
                            ->image()
                            ->directory('kos')
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Foto'),
            ]);
    }
}
