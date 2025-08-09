<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\User;
use App\Models\PlaceImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TelAvivPlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bir place manager kullanıcı seç (ilk mevcut ya da yoksa oluştur)
        $manager = User::whereHas('roles', function ($q) {
            $q->where('name', 'placeManager');
        })->first();
        if (!$manager) {
            // Yedek oluştur
            $manager = User::create([
                'name' => 'TelAviv Manager',
                'email' => 'telaviv-manager@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $manager->assignRole('placeManager');
        }

        $placesData = [
            [
                'name' => 'Sunset Rooftop',
                'description' => 'Panoramik şehir manzarası ve elektronik/house geceleriyle bilinen modern rooftop kulüp.',
            ],
            [
                'name' => 'Basement Vibes',
                'description' => 'Yeraltı techno ve minimal setleriyle Tel Aviv gece hayatının gizli favorisi.',
            ],
            [
                'name' => 'Mediterranean Lounge',
                'description' => 'Akdeniz esintili kokteyller ve chill-out / downtempo DJ performansları.',
            ],
        ];

        foreach ($placesData as $index => $data) {
            $place = Place::firstOrCreate(
                [
                    'user_id' => $manager->id,
                    'name' => $data['name'],
                ],
                [
                    'description' => $data['description'],
                    'location' => 'Tel Aviv, Israel',
                    'is_premium' => $index === 0, // ilkini premium işaretleyelim örnek olsun
                    'premium_expires_at' => $index === 0 ? now()->addMonth() : null,
                    'premium_trial_used' => false,
                    'phone' => '+972-52-000-' . str_pad((string)rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'instagram_url' => 'https://instagram.com/' . Str::slug($data['name']),
                    'facebook_url' => 'https://facebook.com/' . Str::slug($data['name']),
                ]
            );

            // Eğer premium süresi dolmuş ve yeniden seed ediliyorsa ve ilk index ise uzat
            if ($index === 0 && (!$place->premium_expires_at || $place->premium_expires_at->isPast())) {
                $place->update([
                    'is_premium' => true,
                    'premium_expires_at' => now()->addMonth(),
                ]);
            }

            // Görseller yoksa ekle (idempotent)
            if ($place->images()->count() === 0) {
                $imagePaths = [
                    'place-images/sample1.svg',
                    'place-images/sample2.svg',
                    'place-images/sample3.svg',
                ];
                $sort = 1;
                foreach ($imagePaths as $path) {
                    PlaceImage::firstOrCreate(
                        [
                            'place_id' => $place->id,
                            'path' => $path,
                        ],
                        [
                            'alt_text' => $place->name . ' Image ' . $sort,
                            'sort_order' => $sort,
                        ]
                    );
                    $sort++;
                }
            }
        }
    }
}
