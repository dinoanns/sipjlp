<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'pjlp_id',
        'tanggal',
        'shift_id',
        'lokasi_id',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'is_published' => 'boolean',
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

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForPjlp($query, $pjlpId)
    {
        return $query->where('pjlp_id', $pjlpId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeForWeek($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
                     ->whereMonth('tanggal', $month);
    }
}
