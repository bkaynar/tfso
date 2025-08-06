<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser; // Bu satırı ekleyin
use Filament\Panel; // Bu satırı ekleyin
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

/*
    * @OA\Schema(
    *     schema="User",
    *     title="User",
    *     description="Model representing a user in the system",
    *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the user", readOnly=true),
    *     @OA\Property(property="name", type="string", description="Name of the user"),
    *     @OA\Property(property="email", type="string", format="email", description="Email address of the user"),
    *     @OA\Property(property="password", type="string", format="password", description="Password for the user account"),
    *     @OA\Property(property="profile_photo", type="string", description="URL of the user's profile photo"),
    *     @OA\Property(property="bio", type="string", description="Short biography of the user"),
    *     @OA\Property(property="instagram", type="string", description="Instagram handle of the user"),
    *     @OA\Property(property="twitter", type="string", description="Twitter handle of the user"),
    *     @OA\Property(property="facebook", type="string", description="Facebook profile URL of the user"),
    *     @OA\Property(property="youtube", type="string", description="Youtube link of the user"),
    *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp of the user account", readOnly=true),
    *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp of the user account", readOnly=true)
    * )
    */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasTranslations;

    /**
     * Accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'cover_image_url',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'bio',
        'instagram',
        'twitter',
        'facebook',
        'youtube', // tiktok yerine youtube
        'soundcloud',
        'cover_image',
        'iap_product_id',
        'premium',
        'fcm_token', // Add this line

    ];
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // İlişkiler
    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function favoriteSets()
    {
        return $this->belongsToMany(Set::class, 'favorite_sets')->withTimestamps();
    }

    public function favoriteTracks()
    {
        return $this->belongsToMany(Track::class, 'favorite_tracks')->withTimestamps();
    }

    public function favoriteRadios()
    {
        return $this->belongsToMany(Radio::class, 'favorite_radios')->withTimestamps();
    }

    public function favoriteDJs()
    {
        return $this->belongsToMany(User::class, 'favorite_djs', 'user_id', 'favorited_user_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'favorite_djs', 'favorited_user_id', 'user_id')->withTimestamps();
    }

    // Profile photo URL accessor
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    // Cover image URL accessor
    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Spatie Roles kullanarak "admin" rolüne sahip kullanıcıların erişimini sağlayın.
        // Bu, en güvenli ve önerilen yaklaşımdır.
        return $this->hasAnyRole(['admin', 'dj']);

        // Eğer birden fazla role sahip kullanıcıların erişmesini istiyorsanız:
        // return $this->hasAnyRole(['admin', 'editor']);

        // Veya sadece belirli bir e-posta alan adına sahip kullanıcıların erişmesini istiyorsanız (daha az güvenli):
        // return str_ends_with($this->email, '@yourdomain.com');

        // DİKKAT: Aşağıdaki satırı üretim ortamında KULLANMAYIN!
        // return true; // Bu, herkesin paneli görmesine izin verir ve güvenlik açığıdır.
    }
}
