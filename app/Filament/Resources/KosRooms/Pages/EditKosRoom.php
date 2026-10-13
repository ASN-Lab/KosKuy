<?php

namespace App\Filament\Resources\KosRooms\Pages;

use App\Filament\Resources\KosRooms\KosRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKosRoom extends EditRecord
{
    protected static string $resource = KosRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
