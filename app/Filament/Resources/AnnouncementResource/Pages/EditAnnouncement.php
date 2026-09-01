<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use App\Models\Announcement;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function afterSave(): void
    {
        if ($this->record->is_pinned) {
            Announcement::where('id', '!=', $this->record->id)->update(['is_pinned' => false]);
        }
    }
}
