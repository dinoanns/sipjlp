<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'kode',
        'gedung',
        'lantai',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function lembarKerjaDetail(): HasMany
    {
        return $this->hasMany(LembarKerjaDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute(): string
    {
        $parts = [$this->nama];
        if ($this->gedung) {
            $parts[] = "Gedung {$this->gedung}";
        }
        if ($this->lantai) {
            $parts[] = "Lt. {$this->lantai}";
        }
        return implode(' - ', $parts);
    }
}
