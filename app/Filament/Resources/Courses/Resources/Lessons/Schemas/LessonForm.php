<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Schemas;

use App\Models\Lesson;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Lesson Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set, ?string $operation) {
        if ($operation === 'edit') {
            return;
        }
        $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit')
                            ->dehydrated()
                            ->unique(Lesson::class, 'slug', ignoreRecord: true),

                        TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->columnSpanFull(),

                        TextInput::make('duration_minutes')
                            ->label('Duration (minutes)')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        TextInput::make('order')
                            ->label('Order / Position')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Lesson Body')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Settings')
                    ->description('Visibility and accessibility controls.')
                    ->schema([
                        Toggle::make('is_free')
                            ->label('Free Preview')
                            ->helperText('Allow users to watch this without purchasing the course.'),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Make this lesson visible to enrolled students.'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
