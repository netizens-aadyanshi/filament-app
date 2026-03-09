<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserRegistrationChart extends ChartWidget
{
    protected ?string $heading = 'User Registration Chart';

    public ?string $filter = '7';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {

        $activeFilter = (int) $this->filter;

        $data = Trend::model(User::class)
            ->between(
                start: now()->subDays($activeFilter - 1),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users Created',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10B981',
                    'backgroundColor' => '#10B98120',
                    'fill' => true,
                    'tension' => 0.4,

                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M')),
        ];

    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getPollInterval(): ?string
    {
        return '60s';
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 Days',
            '30' => 'Last 30 Days',
            '90' => 'Last 90 Days',

        ];
    }
}
