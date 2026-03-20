<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiRaw extends Model
{
    use HasFactory;

    protected $table = 'absensi_raw';

    protected $fillable = [
        'mesin_id',
        'nip',
        'tanggal_scan',
        'tipe',
        'is_processed',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_scan' => 'datetime',
            'is_processed' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('tanggal_scan', $date);
    }

    public function markAsProcessed(): void
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }
}
