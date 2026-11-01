<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePayments extends ManageRecords
{
    protected static string $resource = PaymentResource::class;

    /**
     * Pembayaran hanya dibuat melalui alur checkout (API), bukan manual dari admin.
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}