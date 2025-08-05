<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = [
        'name',
        'description',
        'google_maps_url',
        'apple_maps_url',
        'location',
        'is_premium',
        'phone',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PlaceImage::class)->orderBy('sort_order');
    }
}
