<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'model_type',
        'model_id',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'waktu' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('waktu', '>=', now()->subDays($days));
    }

    public static function log(
        string $aktivitas,
        ?Model $model = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'aktivitas' => $aktivitas,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'waktu' => now(),
        ]);
    }
}
