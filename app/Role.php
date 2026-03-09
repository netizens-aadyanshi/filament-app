<?php

namespace App;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum Role: string implements HasColor, HasIcon, HasLabel
{
    case Admin = 'admin';

    case Customer = 'customer';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'info',
            self::Customer => 'success',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Admin => Heroicon::OutlinedShieldCheck,
            self::Customer => Heroicon::OutlinedUser,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
