<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembarKerjaDetail extends Model
{
    use HasFactory;

    protected $table = 'lembar_kerja_detail';

    protected $fillable = [
        'lembar_kerja_id',
        'jam',
        'pekerjaan',
        'lokasi_id',
        'keterangan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'jam' => 'datetime:H:i',
        ];
    }

    public function lembarKerja(): BelongsTo
    {
        return $this->belongsTo(LembarKerja::class);
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/lembar-kerja/' . $this->foto);
        }
        return null;
    }
}
