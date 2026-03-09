<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Exports\UserExporter;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('profile_photo')->label('Profile Photo')->circular()->imageHeight(40)
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=random&name='.urlencode($record->name);
                    }),
                TextColumn::make('name')->sortable()->searchable()->copyable()->copyMessage('Name copied!!')->weight(FontWeight::SemiBold)
                    ->summarize(Count::make()->label('Total Users')),
                TextColumn::make('email')->sortable()->searchable()->copyable()->icon(Heroicon::Envelope),
                TextColumn::make('phone_number')->label('Phone Number')->sortable(),
                TextColumn::make('role')->badge(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')

                    ->getStateUsing(fn ($record): bool => filled($record->email_verified_at))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                TextColumn::make('created_at')->label('Registered')->dateTime('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('interests'),
                TextColumn::make('status')->badge(),
                TextColumn::make('bio'),
                TextColumn::make('notes')->label('Internal Notes'),
                TextColumn::make('address')->label('Saved Addresses'),
                TextColumn::make('ban_reason')->label('Ban Reason'),
                TextColumn::make('preferences')->label('Add Preferences'),
                TextColumn::make('label_color')->label('Label Color'),
                TextColumn::make('posts_count')
                    ->counts('posts as posts_count')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->summarize(
                        Sum::make()
                            ->label('Total Posts')
                    ),
                TextColumn::make('published_posts_count')
                    ->counts('posts as published_posts_count', fn ($query) => $query->where('status', 'published'))
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->summarize([
                        Sum::make()
                            ->label('Total Published Posts'),
                        Average::make()
                            ->label('Avg Published'),
                    ]),

            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'banned' => 'Banned',
                    ]),
                TernaryFilter::make('email_verified_at')->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Not verified users'),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_at'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
                    }),
            ])->defaultSort('created_at', 'desc')->striped()
            ->paginated([10, 25, 50])
            ->poll('60s')
            ->emptyStateHeading('No Users Found')
            ->emptyStateDescription('Try adjusting your filters or search terms to find what you are looking for.')
            ->emptyStateIcon('heroicon-o-users')
            ->filtersFormColumns(2)
            ->groups([
                Group::make('role')
                    ->label('Role')
                    ->collapsible(),
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
                ViewAction::make(),
                Action::make('verify_email')
                    ->label('Verify Email')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (User $record) => $record->email_verified_at === null)
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->email_verified_at = now();
                        $record->save();
                    })
                    ->successNotificationTitle('Email Verififed Successfully'),
                Action::make('Suspend Account')
                    ->label('Suspend')
                    ->icon('heroicon-m-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->status->value === 'active')
                    ->requiresConfirmation()
                    ->modalHeading('Suspend Account')
                    ->modalDescription(fn (User $record) => "Are you sure you want to suspend {$record->name}?")
                    ->action(function (User $record) {
                        $record->update([
                            'status' => 'suspended',
                        ]);
                    })
                    ->successNotificationTitle('Suspended the User'),
                Action::make('Ban Account')
                    ->label('Ban')
                    ->icon('heroicon-m-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->status->value !== 'banned')
                    ->requiresConfirmation()
                    ->modalHeading('Ban Account')
                    ->modalDescription(fn (User $record) => "Are you sure you want to Ban {$record->name}?")
                    ->form([
                        Textarea::make('ban_reason')
                            ->label('Reason for Ban')
                            ->placeholder('e.g., Repeated violations of terms of service...')
                            ->required()
                            ->minLength(10)
                            ->maxLength(500)
                            ->helperText('This reason will be logged against the user account.'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'status' => 'banned',
                            'ban_reason' => $data['ban_reason'],
                        ]);
                    })
                    ->successNotificationTitle('Banned the User'),
                DeleteAction::make()
                    ->disabled(fn ($record) => $record->id === auth()->id())
                    ->tooltip(function ($record) {
                        if ($record->id === auth()->id()) {
                            return 'You cannot delete your own account';
                        }

                        return 'Delete this user';
                    })
                    ->modalHeading('Confirm Deletion')
                    ->modalDescription('This will permanently remove the user from the database. This action is irreversible.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make('delete_selected_safe')
                        ->label('Delete selected (Safe)')
                        ->icon('heroicon-m-shield-check')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Safe Delete')
                        ->modalDescription('Selected users will be deleted. Your own account will be skipped automatically.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $recordsToDelete = $records->reject(fn ($record) => $record->id === auth()->id());

                            $recordsToDelete->each->delete();
                        })->successNotificationTitle('Deleted the Bulk User'),

                    ExportBulkAction::make()
                        ->label('Export CSV')
                        ->label('Export CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->exporter(UserExporter::class)
                        ->formats([
                                ExportFormat::Csv,
                            ]),
                ]),
            ]);
    }
}
