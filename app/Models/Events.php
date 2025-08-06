<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Events extends Model
{
  use HasFactory;

    protected $fillable = [
        'user_id',
        'place_id',
        'title',
        'description',
        'date',
        'location',
        'image',
        'ticket_link',
    ];

    // User ilişkisi (event'i oluşturan kullanıcı)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Place ilişkisi (event'in ait olduğu mekan)
    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
