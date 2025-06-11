<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
     protected $primaryKey = 'overtime_id';
    protected $fillable = ['user_id', 'date', 'hours', 'rate', 'total'];
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
