<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAbsensiMesin extends Model
{
    protected $table = 'log_absensi_mesin';

    protected $fillable = [
        'badge_number',
        'check_time',
        'check_type',
        'pjlp_id',
        'is_processed',
    ];

    protected function casts(): array
    {
        return [
            'check_time' => 'datetime',
            'is_processed' => 'boolean',
        ];
    }

    public function pjlp(): BelongsTo
    {
        return $this->belongsTo(Pjlp::class);
    }

    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('check_time', $date);
    }

    public function scopeByBadge($query, $badgeNumber)
    {
        return $query->where('badge_number', $badgeNumber);
    }

    public function scopeCheckIn($query)
    {
        return $query->where('check_type', 'I');
    }

    public function scopeCheckOut($query)
    {
        return $query->where('check_type', 'O');
    }

    /**
     * Get check type label
     */
    public function getCheckTypeLabelAttribute(): string
    {
        return $this->check_type === 'I' ? 'Masuk' : 'Pulang';
    }
}
