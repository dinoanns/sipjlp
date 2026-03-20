<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LembarKerjaCs extends Model
{
    use HasFactory;

    protected $table = 'lembar_kerja_cs';

    protected $fillable = [
        'tanggal',
        'area_id',
        'pjlp_id',
        'shift_id',
        'status',
        'catatan_pjlp',
        'catatan_koordinator',
        'submitted_at',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    // Konstanta status
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_VALIDATED = 'validated';
    const STATUS_REJECTED = 'rejected';

    const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SUBMITTED => 'Menunggu Validasi',
        self::STATUS_VALIDATED => 'Tervalidasi',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    const STATUS_COLORS = [
        self::STATUS_DRAFT => 'secondary',
        self::STATUS_SUBMITTED => 'warning',
        self::STATUS_VALIDATED => 'success',
        self::STATUS_REJECTED => 'danger',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(MasterAreaCs::class, 'area_id');
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class, 'pjlp_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(LembarKerjaCsDetail::class, 'lembar_kerja_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? '-';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function getShiftNamaAttribute(): string
    {
        return $this->shift?->nama ?? '-';
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canValidate(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function submit(): bool
    {
        if (!$this->canSubmit()) {
            return false;
        }

        $this->status = self::STATUS_SUBMITTED;
        $this->submitted_at = now();
        return $this->save();
    }

    public function validate(int $validatorId, ?string $notes = null): bool
    {
        if (!$this->canValidate()) {
            return false;
        }

        $this->status = self::STATUS_VALIDATED;
        $this->validated_by = $validatorId;
        $this->validated_at = now();
        $this->catatan_koordinator = $notes;
        return $this->save();
    }

    public function reject(int $validatorId, ?string $notes = null): bool
    {
        if (!$this->canValidate()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->validated_by = $validatorId;
        $this->validated_at = now();
        $this->catatan_koordinator = $notes;
        return $this->save();
    }

    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->details()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->details()->where('is_completed', true)->count();
        return round(($completed / $total) * 100, 1);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    public function scopeByPjlp($query, $pjlpId)
    {
        return $query->where('pjlp_id', $pjlpId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeByPeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
}
