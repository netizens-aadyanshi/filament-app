<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->description(fn ($record) => $record->slug),

                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min'),

                IconColumn::make('is_free')
                    ->label('Free')
                    ->boolean(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                TextColumn::make('lesson_comments_count')
                    ->counts('lessonComments')
                    ->label('Comments')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')

            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
