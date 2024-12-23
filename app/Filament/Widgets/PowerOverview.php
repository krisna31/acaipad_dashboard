<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Power;
use Illuminate\Support\Facades\DB;

class PowerOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '5s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $avgLatencyTimeINTERNET = Power::query()
            ->whereBetween('arrived_at', [now()->subMinutes(10), now()])
            ->where('location', Power::INTERNET)
            ->select(DB::raw('AVG((julianday(arrived_at) - julianday(sent_at)) * 24 * 60 * 60 * 1000) as avg_diff'))
            ->value('avg_diff') ?? 0;

        $avgLatencyTimeLokal = Power::query()
            ->whereBetween('arrived_at', [now()->subMinutes(10), now()])
            ->where('location', Power::LOKAL)
            ->select(DB::raw('AVG((julianday(arrived_at) - julianday(sent_at)) * 24 * 60 * 60 * 1000) as avg_diff'))
            ->value('avg_diff') ?? 0;

        return [
            Stat::make('Last 10 Minutes of average Latency With Internet', "$avgLatencyTimeINTERNET ms")
                // ->description("Test")
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->chart([14, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Last 10 Minutes of average Latency With Lokal', "$avgLatencyTimeLokal ms")
                // ->description("Test")
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info')
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
