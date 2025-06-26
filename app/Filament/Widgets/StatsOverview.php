<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Category;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kategori Produk', Category::count())
                ->description('Jumlah semua kategori produk')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Total Produk', Product::count())
                ->description('Jumlah semua varian produk')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

        ];
    }
}
