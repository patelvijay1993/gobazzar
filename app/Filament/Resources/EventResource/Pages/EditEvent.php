<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

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
