<?php

declare(strict_types=1);

namespace App\Filament\Resources\TeamUpdateResource\Pages;

use App\Filament\Resources\TeamUpdateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamUpdate extends CreateRecord
{
    protected static string $resource = TeamUpdateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['is_published'] ?? false) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
