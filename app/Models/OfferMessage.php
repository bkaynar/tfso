<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferMessage extends Model
{
    protected $fillable = [
        'offer_id',
        'user_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the offer this message belongs to
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(DjOffer::class, 'offer_id');
    }

    /**
     * Get the user who sent this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    /**
     * Check if message is read
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }
}
