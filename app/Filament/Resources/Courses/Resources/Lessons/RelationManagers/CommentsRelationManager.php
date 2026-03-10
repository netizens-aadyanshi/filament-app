<?php

namespace App\Filament\Resources\Courses\Resources\Lessons\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextArea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('author_name')
                    ->required()
                    ->maxLength(255),

                Toggle::make('is_approved')
                    ->label('Approved')
                    ->default(false),

                Textarea::make('body')
                    ->label('Comment')
                    ->required()
                    ->minLength(5)
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author_name')
            ->columns([
                TextColumn::make('author_name')
                    ->searchable(),

                TextColumn::make('body')
                    ->limit(60)
                    ->tooltip(fn (Model $record) => $record->body),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Posted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
                Action::make('approveAll')
                    ->label('Approve All')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve all comments?')
                    ->modalDescription('Are you sure you want to approve all unapproved comments for this lesson?')
                    ->action(function () {
                        $relationship = $this->getRelationship();

                        $unapprovedCount = $relationship->where('is_approved', false)->count();

                        $relationship->where('is_approved', false)->update(['is_approved' => true]);

                        Notification::make()
                            ->title("Approved {$unapprovedCount} comments successfully.")
                            ->success()
                            ->send();
                    })
                    // Only show the button if there are unapproved comments to process
                    ->visible(fn () => $this->getRelationship()->where('is_approved', false)->exists()),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
