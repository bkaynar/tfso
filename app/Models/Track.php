<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Track extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'audio_file',
        'image_file',
        'duration',
        'is_premium',
        'iap_product_id',
        'category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
