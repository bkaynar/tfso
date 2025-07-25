<?php

// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array', // Assuming value is stored as JSON
    ];

    //hide timestamps in API responses
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static function get($key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
