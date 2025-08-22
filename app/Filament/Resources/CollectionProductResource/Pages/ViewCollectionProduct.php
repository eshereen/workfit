<?php

namespace App\Filament\Resources\CollectionProductResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\CollectionProductResource;
use Filament\Actions;
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
