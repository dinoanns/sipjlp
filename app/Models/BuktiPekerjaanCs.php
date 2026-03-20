<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiPekerjaanCs extends Model
{
    use HasFactory;

    protected $table = 'bukti_pekerjaan_cs';

    protected $fillable = [
        'jadwal_bulanan_id',
        'pjlp_id',
        'foto_bukti',
        'catatan',
        'dikerjakan_at',
        'is_completed',
        'is_validated',
        'is_rejected',
        'validated_by',
        'validated_at',
        'catatan_validator',
    ];

    protected $casts = [
        'dikerjakan_at' => 'datetime',
        'validated_at' => 'datetime',
        'is_completed' => 'boolean',
        'is_validated' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    // Relationships
    public function jadwalBulanan(): BelongsTo
    {
        return $this->belongsTo(JadwalKerjaCsBulanan::class, 'jadwal_bulanan_id');
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class, 'pjlp_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopePendingValidation($query)
    {
        return $query->where('is_completed', true)
            ->where('is_validated', false)
            ->where('is_rejected', false);
    }

    public function scopeRejected($query)
    {
        return $query->where('is_rejected', true);
    }

    public function scopeByPjlp($query, $pjlpId)
    {
        return $query->where('pjlp_id', $pjlpId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereHas('jadwalBulanan', function ($q) use ($tanggal) {
            $q->whereDate('tanggal', $tanggal);
        });
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->is_completed && !$this->is_validated && !$this->is_rejected;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_validated) {
            return 'Tervalidasi';
        }
        if ($this->is_rejected) {
            return 'Ditolak';
        }
        if ($this->is_completed) {
            return 'Menunggu Validasi';
        }
        return 'Belum Dikerjakan';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_validated) {
            return 'success';
        }
        if ($this->is_rejected) {
            return 'danger';
        }
        if ($this->is_completed) {
            return 'warning';
        }
        return 'secondary';
    }
}
