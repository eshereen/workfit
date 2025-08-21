<?php

namespace App\Filament\Resources\CollectionProductResource\Pages;

use App\Filament\Resources\CollectionProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCollectionProducts extends ListRecords
{
    protected static string $resource = CollectionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
