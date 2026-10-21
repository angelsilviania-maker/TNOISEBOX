<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun khusus Admin
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com', // Ubah email admin kamu di sini
            'password' => Hash::make('password123'),
            'role' => 'admin', // <--- INI WAJIB ADA SUPAYA JADI ADMIN
        ]);

        // 2. Akun khusus Customer / Pelanggan
        User::factory()->create([
            'name' => 'Pelanggan',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->call([
            EventSeeder::class,
        ]);
    }
}
