<?php

namespace App\Filament\Resources\CarpoolResource\Pages;

use App\Filament\Resources\CarpoolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarpool extends EditRecord
{
    protected static string $resource = CarpoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Stamp/clear inactive_at so the 7-day auto-delete clock reflects an admin-triggered
    // status change too, not just expires_at passing.
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) !== $this->record->status) {
            $data['inactive_at'] = $data['status'] === 'inactive' ? now() : null;
        }

        return $data;
    }
}
