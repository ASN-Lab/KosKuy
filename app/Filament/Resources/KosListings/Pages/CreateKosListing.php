<?php

namespace App\Filament\Resources\KosListings\Pages;

use App\Filament\Resources\KosListings\KosListingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateKosListing extends CreateRecord
{
    protected static string $resource = KosListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->role === 'owner') {
            $data['owner_id'] = auth()->id();
        }

        return $data;
    }
}
