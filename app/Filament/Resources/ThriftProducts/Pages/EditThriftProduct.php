<?php

namespace App\Filament\Resources\ThriftProducts\Pages;

use App\Filament\Resources\ThriftProducts\ThriftProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditThriftProduct extends EditRecord
{
    protected static string $resource = ThriftProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
