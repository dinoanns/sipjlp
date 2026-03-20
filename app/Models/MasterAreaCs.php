<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAreaCs extends Model
{
    use HasFactory;

    protected $table = 'master_area_cs';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jadwalAktivitas(): HasMany
    {
        return $this->hasMany(JadwalAktivitasCs::class, 'area_id');
    }

    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanAreaCs::class, 'area_id');
    }

    public function lembarKerja(): HasMany
    {
        return $this->hasMany(LembarKerjaCs::class, 'area_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }
}
