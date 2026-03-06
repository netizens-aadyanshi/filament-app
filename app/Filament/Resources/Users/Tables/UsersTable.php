<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn\json_decode;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\DeleteAction;
use App\Models\User;
use Filament\Forms\Components\Textarea;


class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('profile_photo')->label('Profile Photo')->circular()->imageHeight(40)
                ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=random&name=' . urlencode($record->name);
                    }),
                TextColumn::make('name')->sortable()->searchable()->copyable()->copyMessage('Name copied!!')->weight(FontWeight::SemiBold),
                TextColumn::make('email')->sortable()->searchable()->copyable()->icon(Heroicon::Envelope),
                TextColumn::make('phone_number')->label('Phone Number')->sortable(),
                TextColumn::make('role')->badge(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')

                    ->getStateUsing(fn ($record): bool => filled($record->email_verified_at))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success') // Green
                    ->falseColor('danger')  // Red
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
                    ->successNotificationTitle("Email Verififed Successfully"),
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
                    ->successNotificationTitle("Suspended the User"),
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
                    ->successNotificationTitle("Banned the User"),
                    DeleteAction::make()
                    // 1. Disable the button if the record is the currently logged-in user
                    ->disabled(fn ($record) => $record->id === auth()->id())

                    // 2. Add a dynamic tooltip to explain why the button is greyed out
                    ->tooltip(function ($record) {
                        if ($record->id === auth()->id()) {
                            return 'You cannot delete your own account';
                        }
                        return 'Delete this user'; // Optional: Tooltip for other rows
                    })

                    // Customizing the modal for extra safety on other rows
                    ->modalHeading('Confirm Deletion')
                    ->modalDescription('This will permanently remove the user from the database. This action is irreversible.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
