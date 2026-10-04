<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Penyewa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('kos_id')
                    ->label('Kos')
                    ->relationship('kos', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('booking_date')
                    ->label('Tanggal Booking')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan'),
            ]);
    }
}
