<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Salary;

class Allowance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'allowance_id';
    protected $fillable = [
        'name',
        'type',      
        'amount',
        'percentage',
        'description',
    ];


    public function salary()
    {
        return $this->belongsToMany(Salary::class, 'salary_allowance', 'allowance_id', 'salary_id');
    }
}
