<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected  ?string $heading = 'Revenue (Last 3 Months - EGP)';



   protected static ?  int $sort = 2;
    protected function getData(): array
    {
        // Fetch revenue grouped by month (last 3 months only)
        $revenues = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(total_amount) as revenue")
            )
            ->where('currency', 'EGP')
            ->where('created_at', '>=', now()->subMonths(3))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (EGP)',
                    'data' => $revenues->values(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#60a5fa',
                ],
            ],
            'labels' => $revenues->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // You can use 'bar' or 'line'
    }
}
