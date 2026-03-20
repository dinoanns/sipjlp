<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPekerjaanCs extends Model
{
    use HasFactory;

    protected $table = 'master_pekerjaan_cs';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jadwalKerja(): HasMany
    {
        return $this->hasMany(JadwalKerjaCsBulanan::class, 'pekerjaan_id');
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
