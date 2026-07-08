<?php

namespace App\Filament\Resources\TeamUpdateResource\Pages;

use App\Filament\Resources\TeamUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamUpdates extends ListRecords
{
    protected static string $resource = TeamUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
