<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisCuti extends Model
{
    use HasFactory;

    protected $table = 'jenis_cuti';

    protected $fillable = [
        'nama',
        'max_hari',
        'keterangan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function cuti(): HasMany
    {
        return $this->hasMany(Cuti::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
