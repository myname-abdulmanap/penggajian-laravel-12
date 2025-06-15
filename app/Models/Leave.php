<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Leave extends Model
{
    protected $table = 'leave_requests';
    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'leave_type',
        'status',
        'reason',
        'attachment',
    ];

    /**
     * Relasi ke user yang mengajukan cuti.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk mendapatkan cuti berdasarkan status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            do {
                $randomId = random_int(10000000, 99999999); // ID acak 8 digit
            } while (DB::table('leave_requests')->where('leave_id', $randomId)->exists());

            $model->leave_id = $randomId;
        });
    }
}
