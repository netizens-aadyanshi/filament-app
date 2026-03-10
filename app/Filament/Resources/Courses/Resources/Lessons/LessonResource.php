<?php

namespace App\Filament\Resources\Courses\Resources\Lessons;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\Resources\Lessons\Pages\CreateLesson;
use App\Filament\Resources\Courses\Resources\Lessons\Pages\EditLesson;
use App\Filament\Resources\Courses\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Courses\Resources\Lessons\Tables\LessonsTable;
use App\Models\Lesson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Courses\Resources\Lessons\RelationManagers\CommentsRelationManager;
class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = CourseResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateLesson::route('/create'),
            'edit' => EditLesson::route('/{record}/edit'),
        ];
    }
}
