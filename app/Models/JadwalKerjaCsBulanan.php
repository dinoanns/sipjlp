<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalKerjaCsBulanan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kerja_cs_bulanan';

    protected $fillable = [
        'area_id',
        'tanggal',
        'pekerjaan',
        'pekerjaan_id',
        'shift_id',
        'pjlp_id',
        'tipe_pekerjaan',
        'keterangan',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    // Konstanta tipe pekerjaan
    const TIPE_WAJIB_SIPANTAU = 'wajib_sipantau';
    const TIPE_EXTRA_JOB = 'extra_job';

    const TIPE_LABELS = [
        self::TIPE_WAJIB_SIPANTAU => 'Wajib Input SIPANTAU',
        self::TIPE_EXTRA_JOB => 'Extra Job',
    ];

    const TIPE_COLORS = [
        self::TIPE_WAJIB_SIPANTAU => 'danger',  // merah
        self::TIPE_EXTRA_JOB => 'success',       // hijau
    ];

    const TIPE_BG_COLORS = [
        self::TIPE_WAJIB_SIPANTAU => '#f8d7da',  // light red
        self::TIPE_EXTRA_JOB => '#d4edda',       // light green
    ];

    // Relationships
    public function area(): BelongsTo
    {
        return $this->belongsTo(MasterAreaCs::class, 'area_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class, 'pjlp_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function masterPekerjaan(): BelongsTo
    {
        return $this->belongsTo(MasterPekerjaanCs::class, 'pekerjaan_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(LembarKerjaCsDetail::class, 'jadwal_bulanan_id');
    }

    public function buktiPekerjaan()
    {
        return $this->hasOne(BuktiPekerjaanCs::class, 'jadwal_bulanan_id')->latest();
    }

    public function semuaBukti(): HasMany
    {
        return $this->hasMany(BuktiPekerjaanCs::class, 'jadwal_bulanan_id');
    }

    // Accessors
    public function getNamaPekerjaanAttribute(): string
    {
        return $this->masterPekerjaan?->nama ?? $this->pekerjaan ?? '-';
    }

    public function getTipeLabelAttribute(): string
    {
        return self::TIPE_LABELS[$this->tipe_pekerjaan] ?? '-';
    }

    public function getTipeColorAttribute(): string
    {
        return self::TIPE_COLORS[$this->tipe_pekerjaan] ?? 'secondary';
    }

    public function getTipeBgColorAttribute(): string
    {
        return self::TIPE_BG_COLORS[$this->tipe_pekerjaan] ?? '#ffffff';
    }

    public function getShiftColorAttribute(): string
    {
        // Warna berdasarkan shift (konsisten dengan referensi)
        $shiftNama = strtolower($this->shift?->nama ?? '');

        return match ($shiftNama) {
            'pagi' => '#cce5ff',      // biru muda
            'siang' => '#fff3cd',     // kuning/cream
            'malam' => '#f8c8dc',     // pink muda
            default => '#ffffff',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeByBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeByShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    // Helpers
    public function isWajibSipantau(): bool
    {
        return $this->tipe_pekerjaan === self::TIPE_WAJIB_SIPANTAU;
    }

    public function isExtraJob(): bool
    {
        return $this->tipe_pekerjaan === self::TIPE_EXTRA_JOB;
    }
}
