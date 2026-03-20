<?php

namespace App\Enums;

enum UnitType: string
{
    case SECURITY = 'security';
    case CLEANING = 'cleaning';
    case ALL = 'all';

    public function label(): string
    {
        return match($this) {
            self::SECURITY => 'Security',
            self::CLEANING => 'Cleaning Service',
            self::ALL => 'Semua Unit',
        };
    }

    public static function forPjlp(): array
    {
        return [
            self::SECURITY,
            self::CLEANING,
        ];
    }
}
