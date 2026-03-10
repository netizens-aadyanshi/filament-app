<?php

namespace App\Filament\Resources\Courses\Infolists;

use App\Models\Course;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class CoursesInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Section::make('Course Overview')
                    ->description('Primary information about this course.')
                    ->schema([
                        TextEntry::make('title')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('description')
                            ->markdown()
                            ->placeholder('No description provided.'),
                    ])
                    ->collapsible(),

                Section::make('Classification')
                    ->schema([
                        TextEntry::make('category')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('level')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'beginner' => 'success',
                                'intermediate' => 'warning',
                                'advanced' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('lessons_count')
                            ->label('Total Lessons')
                            ->state(fn (Course $record): int => $record->lessons()->count())
                            ->badge()
                            ->icon('heroicon-m-play-circle'),
                    ])
                    ->columns(3) 
                    ->collapsible(),

                Section::make('Media & Pricing')
                    ->schema([
                        ImageEntry::make('thumbnail')
                            ->label('Course Thumbnail')
                            ->disk('public')
                            ->visibility('public')
                            ->height(200),

                        TextEntry::make('price')
                            ->money('INR')
                            ->weight('bold'),

                        IconEntry::make('is_published')
                            ->label('Published Status')
                            ->boolean(),
                    ])
                    ->columns(2)
                    ->collapsible(),

            ]);
    }
}
