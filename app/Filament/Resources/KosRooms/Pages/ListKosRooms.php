<?php

namespace App\Filament\Resources\KosRooms\Pages;

use App\Filament\Resources\KosRooms\KosRoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKosRooms extends ListRecords
{
    protected static string $resource = KosRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
