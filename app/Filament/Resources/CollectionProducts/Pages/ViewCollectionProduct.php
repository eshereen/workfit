<?php

namespace App\Filament\Resources\CollectionProducts\Pages;

use App\Filament\Resources\CollectionProducts\CollectionProductResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCollectionProduct extends ViewRecord
{
    protected static string $resource = CollectionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
