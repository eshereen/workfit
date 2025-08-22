<?php

namespace App\Filament\Resources\CollectionProductResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\CollectionProductResource;
use Filament\Actions;
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
