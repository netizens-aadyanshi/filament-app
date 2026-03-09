<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Status;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

// use Filament\Tables\Columns\Layout\Split;
// use Filament\Tables\Columns\Layout\Stack;
// USE THESE - They are for Infolists/View Pages!

class UsersInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Profile')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name'),

                        TextEntry::make('email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->icon('heroicon-m-phone')
                            ->placeholder('No phone number provided'),

                        ImageEntry::make('profile_photo')
                            ->label('')
                            ->circular()
                            ->imageWidth(80)
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?background=random&name='.urlencode($record->name)),
                    ])
                    ->collapsible(),
                Section::make('Account')
                    ->schema([
                        TextEntry::make('role')->badge(),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('created_at')->label('Member Since')
                            ->dateTime('d M Y ')
                            ->placeholder('Not verified'),
                        TextEntry::make('ban_reason')
                            ->visible(fn (User $record): bool => $record->status === Status::Banned),
                        IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->getStateUsing(fn ($record): bool => filled($record->email_verified_at))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle'),

                    ])
                    ->collapsible()
                    ->columns(2),
                Section::make('Preferences')
                    ->schema([
                        ColorEntry::make('label_color')->label('Label Color')->copyable(),
                        KeyValueEntry::make('preferences'),

                    ])
                    ->collapsed(),

                Section::make('Saved Addresses')
                    ->schema([
                        RepeatableEntry::make('address')->label('Saved Address')
                            ->schema([
                                TextEntry::make('label'),
                                TextEntry::make('street'),
                                TextEntry::make('city'),
                                TextEntry::make('type'),

                            ])
                            ->columns(2),

                    ])
                    ->collapsible(),

            ]);
    }
}
