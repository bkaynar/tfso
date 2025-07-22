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
        'event_date',
        'event_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
