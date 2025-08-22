<?php

namespace App\Filament\Resources\LoyaltyTransactionResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\LoyaltyTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyTransaction extends EditRecord
{
    protected static string $resource = LoyaltyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
