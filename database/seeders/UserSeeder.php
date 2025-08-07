<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->insert([
            [
                'name' => 'AdminUtama',
                'username' => 'AdminUtama',
                'password' => Hash::make('AdminUtama123'),
                'role' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AdminForm',
                'username' => 'AdminForm',
                'password' => Hash::make('AdminForm123'),
                'role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AdminDarah',
                'username' => 'AdminDarah',
                'password' => Hash::make('AdminDarah123'),
                'role' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
