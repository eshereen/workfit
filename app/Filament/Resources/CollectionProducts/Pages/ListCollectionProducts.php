<?php

namespace App\Filament\Resources\CollectionProducts\Pages;

use App\Filament\Resources\CollectionProducts\CollectionProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCollectionProducts extends ListRecords
{
    protected static string $resource = CollectionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
