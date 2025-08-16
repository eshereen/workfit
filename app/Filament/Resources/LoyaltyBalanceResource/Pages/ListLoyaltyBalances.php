<?php

namespace App\Filament\Resources\LoyaltyBalanceResource\Pages;

use App\Filament\Resources\LoyaltyBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyBalances extends ListRecords
{
    protected static string $resource = LoyaltyBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
