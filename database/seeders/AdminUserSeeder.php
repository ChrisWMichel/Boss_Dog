<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'firstname' => 'Admin',
            'lastname' => 'Super',
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }
}
