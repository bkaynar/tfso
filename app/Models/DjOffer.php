<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DjOffer extends Model
{
    protected $fillable = [
        'place_manager_id',
        'dj_id',
        'place_id',
        'event_date',
        'event_time',
        'duration',
        'budget',
        'description',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'duration' => 'decimal:2',
        'budget' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the place manager who sent the offer
     */
    public function placeManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'place_manager_id');
    }

    /**
     * Get the DJ who received the offer
     */
    public function dj(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dj_id');
    }

    /**
     * Get the place for the offer
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Get the messages for this offer
     */
    public function messages(): HasMany
    {
        return $this->hasMany(OfferMessage::class, 'offer_id');
    }

    /**
     * Check if offer is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if offer is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if offer is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if offer is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if offer has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Accept the offer
     */
    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
    }

    /**
     * Reject the offer
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Cancel the offer
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Get unread messages count for a user
     */
    public function getUnreadMessagesCount(int $userId): int
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }
}
