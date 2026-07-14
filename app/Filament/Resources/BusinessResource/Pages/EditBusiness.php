<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $hours = $data['hours'] ?? [];
        if (is_string($hours)) $hours = json_decode($hours, true) ?? [];

        foreach (['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day) {
            $h = $hours[$day] ?? [];
            $data["biz_hours_{$day}_open"]   = $h['open']   ?? '';
            $data["biz_hours_{$day}_close"]  = $h['close']  ?? '';
            $data["biz_hours_{$day}_closed"] = !empty($h['closed']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
