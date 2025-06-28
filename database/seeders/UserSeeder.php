<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $dj = User::firstOrCreate([
            'email' => 'dj@example.com',
        ], [
            'name' => 'DJ User',
            'password' => bcrypt('password'),
        ]);
        $dj->assignRole('dj');

        $this->command->info('Admin ve DJ kullanıcıları oluşturuldu ve rolleri atandı.');
    }
}

