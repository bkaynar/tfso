<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $djRole = Role::firstOrCreate(['name' => 'dj']);

        $this->command->info('Admin ve Dj rolleri oluşturuldu/kontrol edildi.');

    }
}

