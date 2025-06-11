<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deduction;

class DeductionSeeder extends Seeder
{
    public function run(): void
    {
        Deduction::insert([
            [
                'deduction_id' => 1,
                'name' => 'BPJS Kesehatan',
                'type' => 'percentage',
                'percentage' => 1.0,
                'amount' => null,
                'description' => 'Potongan BPJS Kesehatan 1%',
            ],
            [
                'deduction_id' => 2,
                'name' => 'PPH 21',
                'type' => 'percentage',
                'percentage' => 5.0,
                'amount' => null,
                'description' => 'Potongan pajak penghasilan PPH 21 5%',
            ],
            [
                'deduction_id' => 3,
                'name' => 'Denda Telat Masuk',
                'type' => 'fixed',
                'percentage' => null,
                'amount' => 50000,
                'description' => 'Denda keterlambatan masuk kerja',
            ],
            [
                'deduction_id' => 4,
                'name' => 'Potongan Pinjaman',
                'type' => 'fixed',
                'percentage' => null,
                'amount' => 100000,
                'description' => 'Potongan cicilan pinjaman karyawan',
            ],
        ]);
    }
}
