<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::count()),
            Stat::make('Customer', Customer::count()),
            Stat::make('Products', Product::count()),
            Stat::make('Revenue by $', number_format(Order::where('currency', 'USD')
            ->sum('total_amount'), 2) . ' $')->color('success'),
            Stat::make('Revenue by EGP', number_format(Order::where('currency', 'EGP')
            ->sum('total_amount'), 2) . ' EGP'),
            Stat::make('Revenue by SR', number_format(Order::where('currency', 'SR')
            ->sum('total_amount'), 2) . ' SR')




        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }




}
