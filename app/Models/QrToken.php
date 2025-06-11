<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * @property string $token
 * @property string $type
 * @property \Carbon\Carbon $expires_at
 * @property bool $is_used
 * @property int|null $used_by
 * @property \Carbon\Carbon|null $used_at
 */
class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'type',
        'expires_at',
        'is_used',
        'used_by',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Relasi ke user yang menggunakan token.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Periksa apakah token masih valid.
     */
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    /**
     * Tandai token sebagai sudah digunakan.
     */
    public function markAsUsed(int $userId): void
    {
        $this->update([
            'is_used' => true,
            'used_by' => $userId,
            'used_at' => now(),
        ]);
    }

    /**
     * Scope: Hanya token yang masih valid (belum digunakan & belum kadaluarsa).
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_used', false)
                     ->where('expires_at', '>', now());
    }
}
