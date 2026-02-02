<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@teamsphere.com',
        ], [
            'name' => 'Administrator',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        User::firstOrCreate([
            'email' => 'ermin1990@gmail.com',
        ], [
            'name' => 'Ermin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
