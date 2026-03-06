<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn\json_decode;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;

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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
