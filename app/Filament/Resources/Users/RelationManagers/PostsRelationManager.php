<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required()
                    ->live(),

                DateTimePicker::make('published_at')->label('Date Published')->visible(fn (Get $get): bool => $get('status') === 'published'),

                RichEditor::make('body')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'bulletList',
                        'italic',
                        'link',

                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()->sortable(),

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
                    ->placeholder('Not Published'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('publishAll')
                    ->label('Publish All')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Publish All Drafts')
                    ->modalDescription('This will publish all draft posts for this user. Are you sure?')
                    ->action(function () {
                        $user = $this->getOwnerRecord();

                        $publishedCount = Post::where('user_id', $user->id)
                            ->where('status', 'draft')
                            ->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);

                        Notification::make()
                            ->title('Success')
                            ->body("Successfully published {$publishedCount} posts.")
                            ->success()
                            ->send();
                    }),

                AssociateAction::make(),

            ])
            ->recordActions([
                        EditAction::make(),
                        DissociateAction::make(),
                        DeleteAction::make(),
                        ForceDeleteAction::make(),
                        RestoreAction::make(),
                        ViewAction::make(),
                    ])
            ->toolbarActions([
                        BulkActionGroup::make([
                            DissociateBulkAction::make(),
                            DeleteBulkAction::make(),
                            ForceDeleteBulkAction::make(),
                            RestoreBulkAction::make(),
                        ]),
                    ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                        ->withoutGlobalScopes([
                            SoftDeletingScope::class,
                        ]));
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextEntry::make('title'),

                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('Not published yet'),

                TextEntry::make('body')
                    ->label('Content')
                    ->html()
                    ->columnSpanFull(),
            ]);
    }
}
