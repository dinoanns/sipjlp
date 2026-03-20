<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalAktivitasCs extends Model
{
    use HasFactory;

    protected $table = 'jadwal_aktivitas_cs';

    protected $fillable = [
        'area_id',
        'aktivitas_id',
        'hari',
        'shift_id',
        'minggu_ke',
        'catatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Konstanta untuk hari (enum string)
    const HARI = [
        'senin' => 'Senin',
        'selasa' => 'Selasa',
        'rabu' => 'Rabu',
        'kamis' => 'Kamis',
        'jumat' => 'Jumat',
        'sabtu' => 'Sabtu',
        'minggu' => 'Minggu',
    ];

    // Mapping hari ke number (ISO 8601: 1=Senin, 7=Minggu)
    const HARI_NUMBER = [
        'senin' => 1,
        'selasa' => 2,
        'rabu' => 3,
        'kamis' => 4,
        'jumat' => 5,
        'sabtu' => 6,
        'minggu' => 7,
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(MasterAreaCs::class, 'area_id');
    }

    public function aktivitas(): BelongsTo
    {
        return $this->belongsTo(MasterAktivitasCs::class, 'aktivitas_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function getHariNamaAttribute(): string
    {
        return self::HARI[$this->hari] ?? ucfirst($this->hari);
    }

    public function getShiftNamaAttribute(): string
    {
        return $this->shift?->nama ?? '-';
    }

    public static function getHariFromNumber(int $dayOfWeek): string
    {
        $mapping = array_flip(self::HARI_NUMBER);
        return $mapping[$dayOfWeek] ?? 'senin';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeByShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }
}
