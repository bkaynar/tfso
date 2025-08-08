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
        
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        
        $message = OfferMessage::create([
            'offer_id' => $offer->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
        ]);
        
        return response()->json([
            'message' => $message->load('user'),
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
}
