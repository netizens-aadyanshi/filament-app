<?php

namespace App\Filament\Resources\Users\Infolists;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class UsersInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('role'),
            ]);
    }
}
