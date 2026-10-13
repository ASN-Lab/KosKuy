<?php

namespace App\Filament\Resources\KosRooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KosRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kos_listing_id')
                    ->label('Kos')
                    ->relationship(
                        'kosListing',
                        'title',
                        modifyQueryUsing: fn ($query) => $query->when(
                            auth()->user()?->role === 'owner',
                            fn ($q) => $q->where('owner_id', auth()->id())
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('room_number')
                    ->label('Nomor Kamar')
                    ->required()
                    ->maxLength(50),
                TextInput::make('price_per_month')
                    ->label('Harga / Bulan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2),
            ]);
    }
}
