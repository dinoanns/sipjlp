<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAktivitasCs extends Model
{
    use HasFactory;

    protected $table = 'master_aktivitas_cs';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'kategori',
        'frekuensi',
        'satuan_kerja',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jadwalAktivitas(): HasMany
    {
        return $this->hasMany(JadwalAktivitasCs::class, 'aktivitas_id');
    }

    public function lembarKerjaDetail(): HasMany
    {
        return $this->hasMany(LembarKerjaCsDetail::class, 'aktivitas_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
