<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => env('DEFAULT_ADMIN_EMAIL'),
            'password' => bcrypt(env('DEFAULT_ADMIN_PASSWORD')),
            'email_verified_at' => now(),
        ]);
    }
}
