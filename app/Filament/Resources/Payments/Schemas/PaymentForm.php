<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label('Penghuni')
                    ->relationship(
                        'tenant',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->when(
                            auth()->user()?->role === 'owner',
                            fn ($q) => $q->whereHas('kosListing', fn ($k) => $k->where('owner_id', auth()->id()))
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('period')
                    ->label('Periode')
                    ->required()
                    ->maxLength(7)
                    ->placeholder('2026-10')
                    ->rule('regex:/^\d{4}-\d{2}$/'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Lunas',
                        'pending' => 'Belum bayar',
                    ])
                    ->default('paid')
                    ->required(),
                DatePicker::make('paid_at')
                    ->label('Tanggal Bayar'),
                TextInput::make('method')
                    ->label('Metode')
                    ->maxLength(50)
                    ->placeholder('Transfer / Tunai'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2),
            ]);
    }
}
