<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserDJFavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 31;
        $djIds = [19, 20, 21];

        $user = User::find($userId);
        if (!$user) {
            $this->command->warn("ID {$userId} kullanıcısı bulunamadı!");
            return;
        }

        foreach ($djIds as $djId) {
            $dj = User::find($djId);
            if (!$dj) {
                $this->command->warn("ID {$djId} DJ kullanıcısı bulunamadı!");
                continue;
            }

            if (!$user->favoriteDJs()->where('favorited_user_id', $djId)->exists()) {
                $user->favoriteDJs()->attach($djId);
                $this->command->info("Kullanıcı {$userId}, DJ {$djId}'yi favorilere ekledi.");
            } else {
                $this->command->info("Kullanıcı {$userId}, DJ {$djId} zaten favorilerde.");
            }
        }

        $this->command->info("UserDJFavoritesSeeder başarıyla tamamlandı!");
    }
}