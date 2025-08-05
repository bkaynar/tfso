<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name',
        'description',
        'google_maps_url',
        'apple_maps_url',
        'location',
        'images',
        'is_premium',
        'phone',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
    ];

    protected $casts = [
        'images' => 'array',
        'is_premium' => 'boolean',
    ];
}
