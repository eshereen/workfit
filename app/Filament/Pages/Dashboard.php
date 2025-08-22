<?php

namespace App\Filament\Pages;
use Filament\Pages\Page;

use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';


    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RevenueChart::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 12;
    }
}


