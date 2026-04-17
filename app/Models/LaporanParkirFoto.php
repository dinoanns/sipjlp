<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanParkirFoto extends Model
{
    protected $table = 'laporan_parkir_foto';

    protected $fillable = ['laporan_parkir_id', 'path'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanParkir::class, 'laporan_parkir_id');
    }
}
