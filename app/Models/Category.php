<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function sets()
    {
        return $this->hasMany(Set::class);
    }
}
