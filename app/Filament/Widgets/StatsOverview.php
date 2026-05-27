<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All time')
                ->color('primary'),

            Stat::make('Completed Today', Order::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count())
                ->description('Completed orders today')
                ->color('success'),

            Stat::make('Awaiting Payment', Order::where('status', 'payment_pending')->count())
                ->description('Credit card pending')
                ->color('warning'),

            Stat::make('Pay in Store', Order::where('payment_method', 'store')
                ->where('payment_status', 'pending')
                ->where('status', 'completed')
                ->count())
                ->description('Customers paying in store')
                ->color('info'),

            Stat::make('Active Conversations', Order::whereIn('status', [
                    'collecting', 'selecting_size', 'confirming', 'selecting_payment',
                ])->count())
                ->description('Currently chatting')
                ->color('gray'),

            Stat::make('Total Paid', '₪' . number_format((float) Order::where('payment_status', 'paid')->sum('total_price'), 2))
                ->description('Revenue collected')
                ->color('success'),
        ];
    }
}
