<?php

namespace App\Filament\Resources\KosListings\Pages;

use App\Filament\Resources\KosListings\KosListingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKosListing extends EditRecord
{
    protected static string $resource = KosListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
