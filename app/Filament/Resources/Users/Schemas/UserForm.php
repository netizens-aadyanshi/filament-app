<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;


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
                TextInput::make('phone')->label('Phone Number')->tel()->mask('(999) 999-9999')->placeholder('(999) 999-9999'),

            ]);
    }
}
