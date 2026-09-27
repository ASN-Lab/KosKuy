<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp')
                    ->placeholder('Contoh: 08123456789')
                    ->tel()
                    ->maxLength(20)
                    ->pattern('^[0-9]{10,20}$'),
                Select::make('role')
                    ->label('Peran')
                    ->options([
                        'student' => 'Siswa',
                        'owner' => 'Pemilik Kos/Thrift',
                        'admin' => 'Administrator',
                    ])
                    ->default('student')
                    ->searchable(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->minLength(8)
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
