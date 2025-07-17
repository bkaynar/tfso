<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Set;
use App\Models\Track;
use Illuminate\Support\Facades\DB;

class UserFavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 30;
        
        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            $this->command->error("User with ID {$userId} not found.");
            return;
        }

        // Add favorite sets (IDs: 3, 5, 7)
        $favoriteSetIds = [3, 5, 7];
        foreach ($favoriteSetIds as $setId) {
            if (Set::find($setId)) {
                DB::table('favorite_sets')->insertOrIgnore([
                    'user_id' => $userId,
                    'set_id' => $setId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->command->warn("Set with ID {$setId} not found, skipping.");
            }
        }

        // Add favorite tracks (IDs: 5, 6, 7)
        $favoriteTrackIds = [5, 6, 7];
        foreach ($favoriteTrackIds as $trackId) {
            if (Track::find($trackId)) {
                DB::table('favorite_tracks')->insertOrIgnore([
                    'user_id' => $userId,
                    'track_id' => $trackId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->command->warn("Track with ID {$trackId} not found, skipping.");
            }
        }

        // Add favorite DJs (User IDs: 23, 24, 25)
        $favoriteDjIds = [23, 24, 25];
        foreach ($favoriteDjIds as $djId) {
            if (User::find($djId)) {
                DB::table('favorite_djs')->insertOrIgnore([
                    'user_id' => $userId,
                    'favorited_user_id' => $djId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $this->command->warn("DJ with ID {$djId} not found, skipping.");
            }
        }

        $this->command->info("Favorites added successfully for user ID {$userId}.");
    }
}