<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Check if token is valid (not expired and not used)
     */
    public function isValid()
    {
        return !$this->is_used && $this->expires_at > Carbon::now();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Scope for valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope for expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }
}
