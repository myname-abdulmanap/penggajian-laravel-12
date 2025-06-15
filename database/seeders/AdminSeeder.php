<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'users_id'  => rand(10000, 99999),
            'name'      => 'Admin Kantor',
            'email'     => 'admin@gmail.com',
            'role'      => 'admin',
            'password'  => Hash::make('admin123'),
            'status'    => 'aktif',
            'phone'     => '081234567890',
            'address'   => 'Jl. Sudirman No.1, Jakarta',
            'job_title' => 'Administrator',
            'photo'     => null,
        ]);

        // Karyawan Dummy Indonesia
        $karyawans = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'phone' => '081234567891',
                'address' => 'Jl. Melati No. 10, Bandung',
                'job_title' => 'Staff Keuangan',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@gmail.com',
                'phone' => '081234567892',
                'address' => 'Jl. Mawar No. 15, Yogyakarta',
                'job_title' => 'HRD',
            ],
            [
                'name' => 'Dedi Hermawan',
                'email' => 'dedi@gmail.com',
                'phone' => '081234567893',
                'address' => 'Jl. Kenanga No. 3, Surabaya',
                'job_title' => 'Staff IT',
            ],
            [
                'name' => 'Rina Kartika',
                'email' => 'rina@gmail.com',
                'phone' => '081234567894',
                'address' => 'Jl. Anggrek No. 7, Semarang',
                'job_title' => 'Marketing',
            ],
            [
                'name' => 'Agus Wijaya',
                'email' => 'agus@gmail.com',
                'phone' => '081234567895',
                'address' => 'Jl. Cemara No. 21, Bekasi',
                'job_title' => 'Staff Gudang',
            ],
            [
                'name' => 'Fitriani Dewi',
                'email' => 'fitriani@gmail.com',
                'phone' => '081234567896',
                'address' => 'Jl. Dahlia No. 4, Depok',
                'job_title' => 'Admin Operasional',
            ],
            [
                'name' => 'Hendra Saputra',
                'email' => 'hendra@gmail.com',
                'phone' => '081234567897',
                'address' => 'Jl. Teratai No. 18, Tangerang',
                'job_title' => 'Staff Produksi',
            ],
            [
                'name' => 'Yuni Rahmawati',
                'email' => 'yuni@gmail.com',
                'phone' => '081234567898',
                'address' => 'Jl. Flamboyan No. 11, Bogor',
                'job_title' => 'Customer Service',
            ],
            [
                'name' => 'Andi Kurniawan',
                'email' => 'andi@gmail.com',
                'phone' => '081234567899',
                'address' => 'Jl. Merpati No. 2, Malang',
                'job_title' => 'Desainer Grafis',
            ],
        ];

        foreach ($karyawans as $karyawan) {
            User::create([
                'users_id'  => rand(10000, 99999999),
                'name'      => $karyawan['name'],
                'email'     => $karyawan['email'],
                'role'      => 'karyawan',
                'password'  => Hash::make('karyawan123'),
                'status'    => 'aktif',
                'phone'     => $karyawan['phone'],
                'address'   => $karyawan['address'],
                'job_title' => $karyawan['job_title'],
                'photo'     => null,
            ]);
        }
    }
}
