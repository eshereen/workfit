<?php

namespace App\Filament\Resources\SubCategoryResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\SubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubcategory extends ViewRecord
{
    protected static string $resource = SubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
