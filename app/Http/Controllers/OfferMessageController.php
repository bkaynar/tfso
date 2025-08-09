<?php

namespace App\Http\Controllers;

use App\Models\DjOffer;
use App\Models\OfferMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferMessageController extends Controller
{
    /**
     * Store a new message for an offer
     */
    public function store(Request $request, DjOffer $offer)
    {
        $user = Auth::user();

        // Check if user has access to this offer
        if ($offer->place_manager_id !== $user->id && $offer->dj_id !== $user->id) {
            abort(403, 'You do not have access to this offer.');
        }

        // Messaging disabled if offer cancelled
        if ($offer->isCancelled()) {
            abort(403, 'Messaging is disabled for a cancelled offer.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = OfferMessage::create([
            'offer_id' => $offer->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);

        // Load user relation with only needed fields
        $message->load(['user:id,name']);

        return response()->json([
            'message' => $message,
            'success' => true
        ]);
    }

    /**
     * Get messages for an offer
     */
    public function index(DjOffer $offer)
    {
        $user = Auth::user();

        // Check if user has access to this offer
        if ($offer->place_manager_id !== $user->id && $offer->dj_id !== $user->id) {
            abort(403, 'You do not have access to this offer.');
        }

        if ($offer->isCancelled()) {
            abort(403, 'Messaging is disabled for a cancelled offer.');
        }

        $messages = $offer->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read for current user
        $offer->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages
        ]);
    }

    /**
     * Get unread message count for the authenticated user
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $unreadCount = DjOffer::where(function ($query) use ($user) {
            $query->where('place_manager_id', $user->id)
                ->orWhere('dj_id', $user->id);
        })
            ->withCount(['messages as unread_messages_count' => function ($query) use ($user) {
                $query->where('is_read', false)
                    ->where('user_id', '!=', $user->id);
            }])
            ->get()
            ->sum('unread_messages_count');

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }
}
