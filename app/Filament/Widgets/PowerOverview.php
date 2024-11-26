<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Power;

class PowerOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        $powerDayaWIFI = Power::query()
            ->whereBetween('created_at', [now()->subMinutes(10), now()])
            ->where('koneksi', Power::WIFI)
            ->avg('daya') ?? 0;

        $powerDayaBLE = Power::query()
            ->whereBetween('created_at', [now()->subMinutes(10), now()])
            ->where('koneksi', Power::BLE)
            ->avg('daya') ?? 0;

        return [
            Stat::make('Last 10 Minutes of average daya of Wifi', "$powerDayaWIFI Watt")
                // ->description("$powerLastTenMinutes kode presensi dibuat semimggu terakhir")
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->chart([14, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Last 10 Minutes of average of BLE', "$powerDayaBLE Watt")
                // ->description("$powerLastTenMinutes kode presensi dibuat semimggu terakhir")
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
