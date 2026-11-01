<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(2)
                    ->schema([
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
                            ->regex('/^[0-9]{10,20}$/'),
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
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->required(fn (string $context): bool => $context === 'create'),
                    ]),
                Section::make('Informasi Rekening (Untuk Owner)')
                    ->description('Data ini digunakan untuk menerima transfer pembayaran dari penyewa.')
                    ->columns(3)
                    ->visible(fn ($get) => $get('role') === 'owner')
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->placeholder('Contoh: BCA, Mandiri'),
                        TextInput::make('bank_number')
                            ->label('Nomor Rekening'),
                        TextInput::make('bank_holder')
                            ->label('Nama Pemilik Rekening'),
                    ]),
            ]);
    }
}
