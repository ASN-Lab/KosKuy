<?php

namespace App\Filament\Resources\ThriftProducts\Pages;

use App\Filament\Resources\ThriftProducts\ThriftProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateThriftProduct extends CreateRecord
{
    protected static string $resource = ThriftProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->role === 'owner') {
            $data['seller_id'] = auth()->id();
        }

        return $data;
    }
}
