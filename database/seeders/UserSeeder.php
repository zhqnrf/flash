<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat akun admin pertama
        User::create([
            'name' => 'Super Admin',
            'email' => 'alfasoftware18@gmail.com',
            'password' => Hash::make('password123'), // Silakan ganti passwordnya jika mau
        ]);
    }
}