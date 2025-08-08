<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'google_maps_url',
        'apple_maps_url',
        'location',
        'is_premium',
        'premium_expires_at',
        'premium_trial_used',
        'phone',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'youtube_url',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'premium_expires_at' => 'datetime',
        'premium_trial_used' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PlaceImage::class)->orderBy('sort_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if place is premium (considering expiry)
     */
    public function isPremium(): bool
    {
        if (!$this->is_premium) {
            return false;
        }

        // If no expiry date, consider as permanent premium
        if (!$this->premium_expires_at) {
            return true;
        }

        // Check if premium has expired
        if ($this->premium_expires_at->isPast()) {
            // Auto-remove premium status if expired
            $this->update(['is_premium' => false]);
            return false;
        }

        return true;
    }

    /**
     * Check if premium has expired
     */
    public function isPremiumExpired(): bool
    {
        return $this->premium_expires_at && $this->premium_expires_at->isPast();
    }

    /**
     * Get days until premium expires
     */
    public function daysUntilPremiumExpires(): ?int
    {
        if (!$this->premium_expires_at) {
            return null;
        }

        return now()->diffInDays($this->premium_expires_at, false);
    }

    /**
     * Check if eligible for free trial
     */
    public function isEligibleForFreeTrial(): bool
    {
        return !$this->premium_trial_used;
    }

    /**
     * Start free premium trial (2 months)
     */
    public function startFreeTrial(): void
    {
        $this->update([
            'is_premium' => true,
            'premium_expires_at' => now()->addMonths(2),
            'premium_trial_used' => true,
        ]);
    }

    /**
     * Extend premium for given months
     */
    public function extendPremium(int $months): void
    {
        $expiresAt = $this->premium_expires_at && $this->premium_expires_at->isFuture()
            ? $this->premium_expires_at->addMonths($months)
            : now()->addMonths($months);

        $this->update([
            'is_premium' => true,
            'premium_expires_at' => $expiresAt,
        ]);
    }

    /**
     * Remove premium status
     */
    public function removePremium(): void
    {
        $this->update([
            'is_premium' => false,
            'premium_expires_at' => null,
        ]);
    }

    /**
     * Scope to get only premium places (that haven't expired)
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true)
            ->where(function ($q) {
                $q->whereNull('premium_expires_at')
                  ->orWhere('premium_expires_at', '>', now());
            });
    }

    /**
     * Scope to get only non-premium places
     */
    public function scopeRegular($query)
    {
        return $query->where('is_premium', false)
            ->orWhere('premium_expires_at', '<=', now());
    }

    /**
     * Scope to get places with expired premium
     */
    public function scopeExpiredPremium($query)
    {
        return $query->where('is_premium', true)
            ->where('premium_expires_at', '<=', now());
    }
}
