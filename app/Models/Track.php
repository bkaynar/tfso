<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @OA\Schema(
 *     schema="Track",
 *     title="Track",
 *     description="Track model",
 *     @OA\Property(property="id", type="integer", readOnly="true"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="audio_file", type="string"),
 *     @OA\Property(property="image_file", type="string"),
 *     @OA\Property(property="is_premium", type="boolean"),
 *     @OA\Property(property="iap_product_id", type="string"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="audio_url", type="string", readOnly="true"),
 *     @OA\Property(property="image_url", type="string", readOnly="true"),
 *     @OA\Property(property="audio_file_name", type="string", readOnly="true"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true")
 * )
 */
class Track extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'title',
        'audio_file',
        'image_file',
        'is_premium',
        'iap_product_id',
        'category_id',
    ];

    protected $hidden = [
        'liked_by_users_count',
        'description',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_tracks')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Audio URL accessor
    public function getAudioUrlAttribute()
    {
        return $this->audio_file ? asset('storage/' . $this->audio_file) : null;
    }

    // Image URL accessor
    public function getImageUrlAttribute()
    {
        return $this->image_file ? asset('storage/' . $this->image_file) : null;
    }

    // Audio file name accessor
    public function getAudioFileNameAttribute()
    {
        return $this->audio_file ? basename($this->audio_file) : null;
    }
}
