<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;

class TopSellingProductsChart extends ChartWidget
{
    protected  ?string $heading = 'Top 5 Best Selling Products (KPI)';
    protected  string $color = 'success';
    protected static ?int $sort = 3;
    // Full width on dashboard


    protected function getData(): array
    {
        // Query top 5 products by total quantity sold
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->with('product') // eager load product relation
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $topProducts->pluck('total_sold'),
                    'backgroundColor' => [
                        '#f87171', // red
                        '#60a5fa', // blue
                        '#34d399', // green
                        '#fbbf24', // yellow
                        '#a78bfa', // purple
                    ],
                ],
            ],
            'labels' => $topProducts->pluck('product.name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
