<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DjApplication extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'password',
        'intention_letter',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'status',
        'admin_comment',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the user who submitted this application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who approved this application.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve the application.
     */
    public function approve(User $admin, ?string $comment = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_comment' => $comment,
        ]);
    }

    /**
     * Reject the application.
     */
    public function reject(User $admin, ?string $comment = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_comment' => $comment,
        ]);
    }
}
