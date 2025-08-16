<?php

namespace App\Filament\Resources\AnalyticResource\Pages;

use App\Filament\Resources\AnalyticResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAnalytic extends ViewRecord
{
    protected static string $resource = AnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
