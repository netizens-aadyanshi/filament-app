<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn\json_decode;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('profile_photo')->label('Profile Photo')->circular(),
                TextColumn::make('name')->sortable(),
                TextColumn::make('email')->sortable(),
                TextColumn::make('phone_number')->label('Phone Number')->sortable(),
                TextColumn::make('role'),
                TextColumn::make('email_verified_at')->label('Email Verified'),
                TextColumn::make('interests'),
                TextColumn::make('status'),
                TextColumn::make('bio'),
                TextColumn::make('notes')->label('Internal Notes'),
                TextColumn::make('address')->label('Saved Addresses'),
                TextColumn::make('ban_reason')->label('Ban Reason'),
                TextColumn::make('preferences')->label('Add Preferences'),


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
