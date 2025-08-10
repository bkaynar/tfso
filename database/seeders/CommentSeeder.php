<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Set;
use App\Models\Track;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Check if user exists
        $user = User::find(1);
        if (!$user) {
            $this->command->warn('User with ID 1 not found. Skipping comment seeding.');
            return;
        }

        // Sample comments for variety
        $sampleComments = [
            'Harika bir set! 🔥',
            'Bu parça çok güzel 👌',
            'Muhteşem beat!',
            'Favorilerime ekliyorum 💯',
            'Süper mix olmuş',
            'Bu nasıl bir kalite!',
            'Tekrar tekrar dinliyorum',
            'Çok iyi seçim',
            'Efsane parça',
            'Beat drop muhteşem',
            'Bu set bağımlılık yapıyor',
            'Dinlerken kendimden geçiyorum',
            'Müthiş atmosfer',
            'Bu ne güzel bir his',
            'Kaliteli müzik budur',
        ];

        // Add comments to Sets
        $sets = Set::limit(10)->get();
        foreach ($sets as $set) {
            $commentCount = rand(2, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'user_id' => 1,
                    'content' => $sampleComments[array_rand($sampleComments)],
                    'commentable_type' => 'App\Models\Set',
                    'commentable_id' => $set->id,
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subMinutes(rand(10, 1440)), // Random time within last 24 hours
                ]);
            }
        }

        // Add comments to Tracks
        $tracks = Track::limit(10)->get();
        foreach ($tracks as $track) {
            $commentCount = rand(1, 4);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'user_id' => 1,
                    'content' => $sampleComments[array_rand($sampleComments)],
                    'commentable_type' => 'App\Models\Track',
                    'commentable_id' => $track->id,
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subMinutes(rand(10, 1440)), // Random time within last 24 hours
                ]);
            }
        }

        $this->command->info('Comments seeded successfully!');
    }
}