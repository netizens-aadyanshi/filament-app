<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course Details')
                    ->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        Select::make('category')->options([
                            'programming' => 'Programming',
                            'design' => 'Design',
                            'bussiness' => 'Bussiness',
                            'marketing' => 'Marketing'
                        ])->default('programming')->searchable(),
                        Select::make('level')->options([
                            'beginner' => 'Beginner',
                            'intermediate' => 'Intermediate',
                            'advanced' => 'Advanced'
                        ])->default('beginner'),
                        Textarea::make('description')
                            ->nullable()
                            ->rows(3)
                    ])->collapsible(),
                Section::make('Publishing')
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('₹')
                            ->default(0)
                            ->required(),

                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false),

                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('course-thumbnails')
                            ->imageEditor(),
                    ])
                    ->columns(2),
            ]);
    }
}
