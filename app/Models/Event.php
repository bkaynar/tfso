<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'ticket_url',
        'photo',
        'cover_image',
        'event_date',
        'event_time',
        'is_premium',
        'iap_product_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
