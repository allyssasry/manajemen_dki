<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gunakan User::create untuk kontrol langsung apa yang diinsert
        User::create([
            'name' => 'Allyssa Nur Ayu Soraya',
            'username' => 'allyssanuras',
            'email' => 'allyssanuras@gmail.com',
            'role' => 'digital_banking',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        User::create([
            'name' => 'IT Administrator',
            'username' => 'admin_it',
            'email' => 'admin@bankdki.com',
            'role' => 'it',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Kepala Divisi',
            'username' => 'kepala_divisi',
            'email' => 'kepala@bankdki.com',
            'role' => 'kepala_divisi',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
    }
}
