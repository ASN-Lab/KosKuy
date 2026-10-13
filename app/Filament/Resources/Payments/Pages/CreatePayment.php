<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['tenant_id'])) {
            $data['kos_listing_id'] = Tenant::find($data['tenant_id'])?->kos_listing_id;
        }

        return $data;
    }
}
