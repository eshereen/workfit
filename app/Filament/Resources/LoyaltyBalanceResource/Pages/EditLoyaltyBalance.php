<?php

namespace App\Filament\Resources\LoyaltyBalanceResource\Pages;

use App\Filament\Resources\LoyaltyBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyBalance extends EditRecord
{
    protected static string $resource = LoyaltyBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
