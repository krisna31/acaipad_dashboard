<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Power;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        return "Last $activeFilter Minutes of Average Latency Lokal x Internet";
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $startTime = now()->subMinutes(50);
        $endTime = now();

        $avgDiffLatencyInternet = Power::selectRaw('*, strftime("%f", arrived_at) - strftime("%f", sent_at) as diff_latency')
            ->where('location', Power::INTERNET)
            ->whereBetween('arrived_at', [$startTime, $endTime])
            ->get()
            ->groupBy(function ($record) {
                return Carbon::parse($record->arrived_at)->format('H:i');
            })
            ->map(function ($group) {
                return $group->avg('diff_latency');
            });

        $avgDiffLatencyLokal = Power::selectRaw('*, strftime("%f", arrived_at) - strftime("%f", sent_at) as diff_latency')
            ->where('location', Power::LOKAL)
            ->whereBetween('arrived_at', [$startTime, $endTime])
            ->get()
            ->groupBy(function ($record) {
                return Carbon::parse($record->arrived_at)->format('H:i');
            })
            ->map(function ($group) {
                return $group->avg('diff_latency');
            });

        $datasets = [
            [
                'label' => 'Internet',
                'data' => $avgDiffLatencyInternet->values(),
                'borderColor' => '#34eb65',
            ],
            [
                'label' => 'Lokal',
                'data' => $avgDiffLatencyLokal->values(),
                'borderColor' => '#3446eb',
            ],
        ];

        $labels = $avgDiffLatencyInternet->keys();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
