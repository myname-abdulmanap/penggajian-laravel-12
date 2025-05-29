<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{


    protected $primaryKey = 'attendance_id';
    protected $fillable = [
        'users_id',
        'date',
        'check_in',
        'check_out',
        'location',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
