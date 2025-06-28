<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * @OA\Schema(
 *     schema="Radio",
 *     title="Radio",
 *     description="Radio station model",
 *     @OA\Property(property="id", type="integer", readOnly="true"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="stream_url", type="string"),
 *     @OA\Property(property="logo", type="string", nullable=true),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="logo_url", type="string", readOnly="true", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true")
 * )
 */
class Radio extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'image',
        'stream_url',
        'is_premium',
    ];

    public array $translatable = ['name'];
}
