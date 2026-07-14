<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $hours = [];
        foreach (['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day) {
            $closed = !empty($data["biz_hours_{$day}_closed"]);
            $open   = trim($data["biz_hours_{$day}_open"]  ?? '');
            $close  = trim($data["biz_hours_{$day}_close"] ?? '');

            if ($closed) {
                $hours[$day] = ['closed' => true];
            } elseif ($open || $close) {
                $hours[$day] = ['open' => $open, 'close' => $close];
            }

            unset($data["biz_hours_{$day}_open"], $data["biz_hours_{$day}_close"], $data["biz_hours_{$day}_closed"]);
        }

        $data['hours'] = empty($hours) ? null : $hours;
        return $data;
    }
}
