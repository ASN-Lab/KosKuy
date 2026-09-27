<?php

namespace App\Filament\Resources\KosListings\Pages;

use App\Filament\Resources\KosListings\KosListingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKosListings extends ListRecords
{
    protected static string $resource = KosListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
