<?php

namespace App\Filament\Resources\PaymentHistoryResource\Pages;

use App\Filament\Resources\PaymentHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentHistory extends ViewRecord
{
    protected static string $resource = PaymentHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
