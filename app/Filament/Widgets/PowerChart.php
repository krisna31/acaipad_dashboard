<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Power;
use Illuminate\Support\Carbon;

class PowerChart extends ChartWidget
{
    protected static ?string $heading = 'Last 30 Minutes of Sum Daya';

    protected int|string|array $columnSpan = 6;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $powerWifi = Trend::query(Power::where('koneksi', Power::WIFI))
            ->between(
                start: now()->subMinutes(30),
                end: now(),
            )
            ->perMinute()
            ->sum('daya');

        $powerBle = Trend::query(Power::where('koneksi', Power::BLE))
            ->between(
                start: now()->subMinutes(30),
                end: now(),
            )
            ->perMinute()
            ->sum('daya');

        return [
            'datasets' => [
                [
                    'label' => 'Daya Wifi',
                    'data' => $powerWifi->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#34eb65',
                ],
                [
                    'label' => 'Daya Bluetooth',
                    'data' => $powerBle->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3446eb',
                ],
            ],
            'labels' => $powerWifi->map(fn (TrendValue $value) => Carbon::parse($value->date)->format("H:i:s")),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
