<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Primary key untuk table users
     */
    protected $primaryKey = 'users_id';
    
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'users_id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
        'job_title',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the route key for the model.
     * Ini untuk route model binding menggunakan users_id
     */
    public function getRouteKeyName()
    {
        return 'users_id';
    }

    /**
     * Accessor untuk mendapatkan URL foto
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo && \Storage::disk('public')->exists($this->photo)) {
            return \Storage::url($this->photo);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Override method find untuk menggunakan users_id
     */
    public static function findByUsersId($users_id)
    {
        return static::where('users_id', $users_id)->first();
    }

    /**
     * Override method findOrFail untuk menggunakan users_id
     */
    public static function findByUsersIdOrFail($users_id)
    {
        return static::where('users_id', $users_id)->firstOrFail();
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         do {
    //             $randomId = random_int(10000000, 99999999); // ID acak 8 digit
    //         } while (DB::table('users')->where('users_id', $randomId)->exists());

    //         $model->users_id = $randomId;
    //     });
    // }


}
