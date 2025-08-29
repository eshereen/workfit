<?php

namespace App\Filament\Resources\CollectionProducts\Pages;

use App\Filament\Resources\CollectionProducts\CollectionProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCollectionProduct extends EditRecord
{
    protected static string $resource = CollectionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
