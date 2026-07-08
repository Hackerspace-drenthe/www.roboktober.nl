<?php

namespace App\Filament\Resources\TeamUpdateResource\Pages;

use App\Filament\Resources\TeamUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamUpdate extends EditRecord
{
    protected static string $resource = TeamUpdateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
