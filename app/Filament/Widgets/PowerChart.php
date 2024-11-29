<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Power;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class PowerChart extends ChartWidget
{
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '10';

    protected static ?int $sort = 2;

    protected function getFilters(): ?array
    {
        return [
            '10' => '10 Minutes',
            '20' => '20 Minutes',
            '30' => '30 Minutes',
            '40' => '40 Minutes',
            '50' => '50 Minutes',
            '60' => '60 Minutes',
            '120' => '120 Minutes',
            '180' => '180 Minutes',
        ];
    }

    public function getHeading(): string | Htmlable | null
    {
        $activeFilter = $this->filter;

        return "Last $activeFilter Minutes of Sum Wifi x Bluetooth";
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $powerWifi = Trend::query(Power::where('koneksi', Power::WIFI))
            ->between(
                start: now()->subMinutes(value: $activeFilter),
                end: now(),
            )
            ->perMinute()
            ->sum('daya');

        $powerBle = Trend::query(Power::where('koneksi', Power::BLE))
            ->between(
                start: now()->subMinutes($activeFilter),
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
