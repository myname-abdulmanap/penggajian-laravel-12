<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allowance;

class AllowanceSeeder extends Seeder
{
    public function run(): void
    {
        Allowance::insert([
            [
                'allowance_id' => 1,
                'name' => 'Tunjangan Jabatan',
                'type' => 'fixed',
                'percentage' => null,
                'amount' => 1500000,
                'description' => 'Tunjangan untuk posisi/jabatan karyawan',
            ],
            [
                'allowance_id' => 2,
                'name' => 'Tunjangan Transportasi',
                'type' => 'fixed',
                'percentage' => null,
                'amount' => 500000,
                'description' => 'Tunjangan biaya transportasi harian',
            ],
            [
                'allowance_id' => 3,
                'name' => 'Tunjangan Makan',
                'type' => 'fixed',
                'percentage' => null,
                'amount' => 300000,
                'description' => 'Tunjangan biaya makan selama kerja',
            ],
            [
                'allowance_id' => 4,
                'name' => 'Tunjangan Kesehatan',
                'type' => 'percentage',
                'percentage' => 2.0,
                'amount' => null,
                'description' => 'Tunjangan kesehatan 2% dari gaji pokok',
            ],
        ]);
    }
}
