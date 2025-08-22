<?php

namespace App\Filament\Resources\AnalyticResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\AnalyticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnalytic extends EditRecord
{
    protected static string $resource = AnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
