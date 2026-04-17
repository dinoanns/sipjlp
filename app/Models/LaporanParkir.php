<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanParkir extends Model
{
    protected $table = 'laporan_parkir';

    protected $fillable = [
        'pjlp_id',
        'shift_id',
        'tanggal',
        'jenis',
        'jumlah_kendaraan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(LaporanParkirFoto::class);
    }

    public function scopeByBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function getJenisLabelAttribute(): string
    {
        return match($this->jenis) {
            'roda_4' => 'Roda 4',
            'roda_2' => 'Roda 2',
            default  => $this->jenis,
        };
    }
}
