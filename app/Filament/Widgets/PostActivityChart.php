<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PostActivityChart extends ChartWidget
{
    protected ?string $heading = 'Post Activity - Last 30 Days';

    protected int|string|array $columnSpan = 1;

    public ?string $filter = '7';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $activeFilter = (int) $this->filter;

        $data = Trend::model(Post::class)
            ->between(
                start: now()->subDays($activeFilter - 1),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Posts Created',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $this->getColor('primary'),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('d M')),
        ];
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
