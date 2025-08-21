<?php

namespace App\Filament\Resources\CollectionProductResource\Pages;

use App\Filament\Resources\CollectionProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollectionProduct extends EditRecord
{
    protected static string $resource = CollectionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
