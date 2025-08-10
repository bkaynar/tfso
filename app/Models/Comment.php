<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     title="Comment",
 *     description="Yorum modeli",
 *     @OA\Property(property="id", type="integer", format="int64", description="Yorum ID'si", readOnly=true, example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", description="Yorum yapan kullanıcının ID'si", example=1),
 *     @OA\Property(property="content", type="string", description="Yorum içeriği", maxLength=1000, example="Harika bir set! 🔥"),
 *     @OA\Property(property="commentable_type", type="string", description="Yorum yapılan içerik tipi", example="App\\Models\\Set"),
 *     @OA\Property(property="commentable_id", type="integer", format="int64", description="Yorum yapılan içeriğin ID'si", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Yorum oluşturulma tarihi", readOnly=true, example="2025-08-10T12:00:00.000000Z"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="Yorum yapan kullanıcı bilgileri",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Admin Burak"),
 *         @OA\Property(property="profile_photo", type="string", nullable=true, example="https://example.com/storage/users/profiles/user1.jpg")
 *     )
 * )
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'commentable_id',
        'commentable_type',
        'ip_address',
    ];

    protected $hidden = [
        'ip_address',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
