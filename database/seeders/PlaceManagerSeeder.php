<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Place;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PlaceManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create placeManager role if it doesn't exist
        $placeManagerRole = Role::firstOrCreate(['name' => 'placeManager']);

        // Create a PlaceManager user or get existing one
        $placeManager = User::firstOrCreate(
            ['email' => 'placemanager@example.com'],
            [
                'name' => 'Mekan Yöneticisi',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Assign placeManager role if not already assigned
        if (!$placeManager->hasRole('placeManager')) {
            $placeManager->assignRole('placeManager');
        }

        // Create a place for this manager or get existing one
        $place = Place::firstOrCreate(
            ['user_id' => $placeManager->id],
            [
                'name' => 'Club Paradise',
                'location' => 'Beyoğlu, İstanbul',
                'description' => 'Modern club with state-of-the-art sound system and lighting. Perfect for electronic music events and DJ performances.',
                'is_premium' => true,
                'premium_expires_at' => now()->addMonths(2), // Free 2 months premium
                'premium_trial_used' => false,
                'phone' => '+90 212 555 0123',
                'instagram_url' => 'https://instagram.com/clubparadise',
                'facebook_url' => 'https://facebook.com/clubparadise',
            ]
        );

        $this->command->info('PlaceManager user created:');
        $this->command->info('Email: placemanager@example.com');
        $this->command->info('Password: password');
        $this->command->info('Place: ' . $place->name);
    }
}