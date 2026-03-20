<?php

namespace App\Enums;

enum StatusLembarKerja: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case DIVALIDASI = 'divalidasi';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Menunggu Validasi',
            self::DIVALIDASI => 'Divalidasi',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'secondary',
            self::SUBMITTED => 'warning',
            self::DIVALIDASI => 'success',
            self::DITOLAK => 'danger',
        };
    }
}
