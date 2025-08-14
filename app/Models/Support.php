<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = 'support';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'subject',
        'message',
    ];
}
