<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembarKerjaCsDetail extends Model
{
    use HasFactory;

    protected $table = 'lembar_kerja_cs_detail';

    protected $fillable = [
        'lembar_kerja_id',
        'aktivitas_id',
        'jadwal_bulanan_id',
        'is_dikerjakan',
        'is_selesai',
        'dikerjakan_at',
        'is_completed',
        'jam_mulai',
        'jam_selesai',
        'foto_sebelum',
        'foto_sesudah',
        'foto_bukti',
        'catatan',
        'alasan_tidak_dikerjakan',
    ];

    protected $casts = [
        'is_dikerjakan' => 'boolean',
        'is_selesai' => 'boolean',
        'is_completed' => 'boolean',
        'dikerjakan_at' => 'datetime',
    ];

    // Relationships
    public function lembarKerja(): BelongsTo
    {
        return $this->belongsTo(LembarKerjaCs::class, 'lembar_kerja_id');
    }

    public function aktivitas(): BelongsTo
    {
        return $this->belongsTo(MasterAktivitasCs::class, 'aktivitas_id');
    }

    public function jadwalBulanan(): BelongsTo
    {
        return $this->belongsTo(JadwalKerjaCsBulanan::class, 'jadwal_bulanan_id');
    }

    /**
     * Upload bukti dan set timestamp otomatis
     */
    public function uploadBukti(string $fotoBukti, ?string $catatan = null): bool
    {
        $this->foto_bukti = $fotoBukti;
        $this->dikerjakan_at = now();
        $this->is_completed = true;

        if ($catatan) {
            $this->catatan = $catatan;
        }

        return $this->save();
    }

    /**
     * Cek apakah masih bisa diisi berdasarkan waktu shift
     */
    public function canFillNow(): bool
    {
        $shift = $this->jadwalBulanan?->shift;

        if (!$shift) {
            return false;
        }

        $now = now();
        $tanggal = $this->lembarKerja?->tanggal;

        if (!$tanggal || !$tanggal->isToday()) {
            return false;
        }

        $jamMasuk = $shift->jam_masuk;
        $jamKeluar = $shift->jam_keluar;

        // Parse jam shift
        $shiftStart = $tanggal->copy()->setTimeFromTimeString($jamMasuk);
        $shiftEnd = $tanggal->copy()->setTimeFromTimeString($jamKeluar);

        // Handle shift malam (melewati tengah malam)
        if ($shiftEnd < $shiftStart) {
            $shiftEnd->addDay();
        }

        // Tambah toleransi 30 menit sebelum dan sesudah shift
        $toleransi = 30;
        $shiftStart->subMinutes($toleransi);
        $shiftEnd->addMinutes($toleransi);

        return $now->between($shiftStart, $shiftEnd);
    }

    /**
     * Get warna berdasarkan tipe pekerjaan dari jadwal bulanan
     */
    public function getTipeBgColorAttribute(): string
    {
        return $this->jadwalBulanan?->tipe_bg_color ?? '#ffffff';
    }

    /**
     * Get warna berdasarkan shift dari jadwal bulanan
     */
    public function getShiftBgColorAttribute(): string
    {
        return $this->jadwalBulanan?->shift_color ?? '#ffffff';
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeByAktivitas($query, $aktivitasId)
    {
        return $query->where('aktivitas_id', $aktivitasId);
    }

    public function scopeByJadwalBulanan($query, $jadwalBulananId)
    {
        return $query->where('jadwal_bulanan_id', $jadwalBulananId);
    }
}
