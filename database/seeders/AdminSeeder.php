<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run()
    {
        User::create([
            'name' => 'Admin Kantor',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
    }
}


