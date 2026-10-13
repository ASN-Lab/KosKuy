<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        $ownerScope = fn ($query) => $query->when(
            auth()->user()?->role === 'owner',
            fn ($q) => $q->where('owner_id', auth()->id())
        );

        return $schema
            ->components([
                Select::make('kos_listing_id')
                    ->label('Kos')
                    ->relationship('kosListing', 'title', modifyQueryUsing: $ownerScope)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),
                Select::make('kos_room_id')
                    ->label('Kamar')
                    ->relationship(
                        'room',
                        'room_number',
                        modifyQueryUsing: fn ($query) => $query->when(
                            auth()->user()?->role === 'owner',
                            fn ($q) => $q->whereHas('kosListing', fn ($k) => $k->where('owner_id', auth()->id()))
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->placeholder('Belum ditentukan'),
                TextInput::make('name')
                    ->label('Nama Penghuni')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('No. WhatsApp')
                    ->tel()
                    ->maxLength(30),
                TextInput::make('monthly_price')
                    ->label('Iuran / Bulan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                DatePicker::make('start_date')
                    ->label('Mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Selesai')
                    ->afterOrEqual('start_date'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'checked_out' => 'Sudah keluar',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2),
            ]);
    }
}
