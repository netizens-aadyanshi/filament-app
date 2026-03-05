<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Hidden;


use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')->password()->dehydrated(fn ($state) => filled($state))->required(fn(string $operation) => $operation === 'create')->minLength(8),
                TextInput::make('phone_number')->label('Phone Number')->tel()->mask('(999) 999-9999')->placeholder('(999) 999-9999'),
                Select::make('role')->options([
                    'admin' => 'Admin',
                    'customer' => 'Customer',
                // ])->default('customer')->required()->searchable()->native(false)->multiple(true),
                ])->default('customer')->required()->searchable()->native(false),
                Checkbox::make('agree_to_terms')
                    ->label('I agree to the terms and conditions')
                    ->required()
                    ->dehydrated(false),

                Toggle::make('is_email_verified')
                ->label('Mark Email as Verified')
                ->inlineLabel()
                ->dehydrated(false)
                ->afterStateHydrated(function ($component, $record) {
                    $component->state($record?->email_verified_at !== null);
                })
                ->afterStateUpdated(function ($state, $record) {
                    $record->email_verified_at = $state ? now() : null;
                    $record->save();
                }),
            ]);
    }
}
