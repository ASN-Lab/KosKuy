<?php

namespace App\Filament\Resources\ThriftProducts\Pages;

use App\Filament\Resources\ThriftProducts\ThriftProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThriftProducts extends ListRecords
{
    protected static string $resource = ThriftProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
