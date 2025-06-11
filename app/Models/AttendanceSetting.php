<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_masuk',
        'jam_pulang',
    ];

    public static function getCurrent()
    {
        return self::first() ?? self::create([
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
        ]);
    }
}
