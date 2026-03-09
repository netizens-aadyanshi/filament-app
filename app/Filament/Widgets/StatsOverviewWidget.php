<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
            Stat::make('Active Users', User::where('status', 'active')->count())
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Suspended Users', User::where('status', 'suspended')->count())
                ->description('Temporarily suspended')
                ->descriptionIcon('heroicon-m-pause-circle')
                ->color('warning'),

            Stat::make('Banned Users', User::where('status', 'banned')->count())
                ->description('Permanently banned')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('danger'),

            Stat::make('Unverified Users', User::whereNull('email_verified_at')->count())
                ->description('Email not verified')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('Total Posts', Post::count())
                ->description('All user posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
