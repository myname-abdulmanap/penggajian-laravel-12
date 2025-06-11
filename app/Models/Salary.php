<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $primaryKey = 'salary_id';
    protected $fillable = ['user_id', 'period', 'base_salary', 'allowance', 'overtime', 'deduction', 'net_salary'];
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
