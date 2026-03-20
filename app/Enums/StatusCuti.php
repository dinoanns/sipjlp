<?php

namespace App\Enums;

enum StatusCuti: string
{
    case MENUNGGU = 'menunggu';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match($this) {
            self::MENUNGGU => 'Menunggu',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::MENUNGGU => 'warning',
            self::DISETUJUI => 'success',
            self::DITOLAK => 'danger',
        };
    }
}
