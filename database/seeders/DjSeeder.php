<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DjApplication;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DjSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create dj role if it doesn't exist
        $djRole = Role::firstOrCreate(['name' => 'dj']);

        // Create a DJ user or get existing one
        $dj = User::firstOrCreate(
            ['email' => 'dj@example.com'],
            [
                'name' => 'DJ Test',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'bio' => 'Professional DJ specializing in electronic music and live performances.',
                'location' => 'Istanbul, Turkey',
                'instagram' => 'https://instagram.com/djtest',
                'twitter' => 'https://twitter.com/djtest',
                'soundcloud' => 'https://soundcloud.com/djtest',
            ]
        );

        // Assign DJ role if not already assigned
        if (!$dj->hasRole('dj')) {
            $dj->assignRole('dj');
        }

        // Create an approved DJ application for this user
        $application = DjApplication::firstOrCreate(
            ['user_id' => $dj->id],
            [
                'name' => $dj->name,
                'email' => $dj->email,
                'phone' => '+90 555 123 4567',
                'password' => Hash::make('password'),
                'intention_letter' => 'I am passionate about electronic music and have been DJing for over 5 years. I specialize in house, techno, and progressive music. I have performed at various clubs and events, and I am looking forward to expanding my career by working with quality venues.',
                'facebook' => 'https://facebook.com/djtest',
                'instagram' => 'https://instagram.com/djtest',
                'twitter' => 'https://twitter.com/djtest',
                'youtube' => 'https://youtube.com/djtest',
                'status' => 'approved',
                'approved_at' => now(),
                'admin_comment' => 'Excellent application with good experience.',
            ]
        );

        $this->command->info('DJ user created:');
        $this->command->info('Email: dj@example.com');
        $this->command->info('Password: password');
        $this->command->info('Status: Approved DJ Application');
    }
}