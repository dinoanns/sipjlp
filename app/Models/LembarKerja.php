<?php

namespace App\Models;

use App\Enums\StatusLembarKerja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LembarKerja extends Model
{
    use HasFactory;

    protected $table = 'lembar_kerja';

    protected $fillable = [
        'pjlp_id',
        'tanggal',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'status' => StatusLembarKerja::class,
        ];
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(LembarKerjaDetail::class);
    }

    public function validasi(): HasOne
    {
        return $this->hasOne(LembarKerjaValidasi::class);
    }

    public function scopeForPjlp($query, $pjlpId)
    {
        return $query->where('pjlp_id', $pjlpId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', StatusLembarKerja::SUBMITTED);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, [StatusLembarKerja::DRAFT, StatusLembarKerja::DITOLAK]);
    }

    public function canBeSubmitted(): bool
    {
        return $this->status === StatusLembarKerja::DRAFT && $this->details()->count() > 0;
    }

    public function submit(): void
    {
        $this->update(['status' => StatusLembarKerja::SUBMITTED]);
    }

    public function validate(User $user, ?string $catatan = null): void
    {
        $this->update(['status' => StatusLembarKerja::DIVALIDASI]);

        $this->validasi()->updateOrCreate(
            ['lembar_kerja_id' => $this->id],
            [
                'validated_by' => $user->id,
                'validated_at' => now(),
                'catatan' => $catatan,
            ]
        );
    }

    public function reject(User $user, string $catatan): void
    {
        $this->update(['status' => StatusLembarKerja::DITOLAK]);

        $this->validasi()->updateOrCreate(
            ['lembar_kerja_id' => $this->id],
            [
                'validated_by' => $user->id,
                'validated_at' => now(),
                'catatan' => $catatan,
            ]
        );
    }
}
