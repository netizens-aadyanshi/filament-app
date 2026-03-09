<?php

namespace App;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case Active = 'active';

    case Suspended = 'suspended';

    case Banned = 'banned';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Banned => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Active => 'heroicon-m-check-circle',
            self::Suspended => 'heroicon-m-pause-circle',
            self::Banned => 'heroicon-m-x-circle',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
