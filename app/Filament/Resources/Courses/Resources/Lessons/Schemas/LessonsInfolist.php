<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Infolists;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class LessonsInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Lesson Overview')
                    ->schema([
                        TextEntry::make('course.title')
                            ->label('Course')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('title')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('slug')
                            ->color('gray')
                            ->fontFamily('mono'),
                    ])->columns(3),

                Section::make('Content & Media')
                    ->schema([
                        TextEntry::make('video_url')
                            ->label('Video')
                            ->url()
                            ->color('primary')
                            ->icon('heroicon-m-play-circle')
                            ->placeholder('No video URL provided'),

                        TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->suffix(' minutes'),

                        TextEntry::make('content')
                            ->html()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Status')
                    ->schema([
                        IconEntry::make('is_free')
                            ->label('Free Preview')
                            ->boolean(),

                        IconEntry::make('is_published')
                            ->label('Published')
                            ->boolean(),
                    ])->columns(2),
            ]);
    }
}
