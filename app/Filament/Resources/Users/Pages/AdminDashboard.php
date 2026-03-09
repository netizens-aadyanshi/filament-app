<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PostActivityChart;
use App\Filament\Widgets\RecentPostsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UserRegistrationChart;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            PostActivityChart::class,
            UserRegistrationChart::class,
            RecentPostsWidget::class,
        ];
    }
}
