<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPostsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Post::latest()->limit(10)->with('user')
            )
            ->columns([

                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                    }),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->placeholder('Draft'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])

            ->paginated(false)

            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
