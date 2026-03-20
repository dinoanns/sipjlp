<?php

namespace App\Enums;

enum StatusPjlp: string
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'nonaktif';
    case CUTI = 'cuti';

    public function label(): string
    {
        return match($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Non-Aktif',
            self::CUTI => 'Sedang Cuti',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AKTIF => 'success',
            self::NONAKTIF => 'secondary',
            self::CUTI => 'warning',
        };
    }
}
