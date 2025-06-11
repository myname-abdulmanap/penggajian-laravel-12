<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Salary extends Model
{
    protected $table = 'salaries';
    protected $primaryKey = 'salary_id';
    public $incrementing = false;
    protected $fillable = [
        'users_id',
        'period',
        'base_salary',
        'overtime',
        'net_salary',
        'total_attendance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'users_id');
    }


    public function allowances()
    {
        return $this->belongsToMany(Allowance::class, 'salary_allowance', 'salary_id', 'allowance_id');
    }

    public function deductions()
    {
        return $this->belongsToMany(Deduction::class, 'salary_deduction', 'salary_id', 'deduction_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            do {
                $randomId = random_int(10000000, 99999999); // ID acak 8 digit
            } while (DB::table('salaries')->where('salary_id', $randomId)->exists());

            $model->salary_id = $randomId;
        });
    }
}
