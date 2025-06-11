<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Salary;

class Deduction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $primaryKey = 'deduction_id';
    protected $fillable = [
        'name',
        'type',      
        'amount',
        'percentage',
        'description',
    ];


    public function salary()
    {
        return $this->belongsToMany(Salary::class, 'salary_deduction', 'deduction_id', 'salary_id');
    }
}
