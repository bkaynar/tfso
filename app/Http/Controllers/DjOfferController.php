<?php

namespace App\Http\Controllers;

use App\Models\DjOffer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DjOfferController extends Controller
{
    /**
     * Display DJ list for place managers
     */
    public function djList()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can access DJ list.');
        }
        
        $djs = User::whereHas('roles', function($query) {
            $query->where('name', 'dj');
        })->with(['djApplications' => function($query) {
            $query->where('status', 'approved');
        }])->get();
        
        return Inertia::render('offers/DjList', [
            'djs' => $djs
        ]);
    }
    
    /**
     * Show form to create offer to DJ
     */
    public function create(User $dj)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can create offers.');
        }
        
        // Get place manager's place
        $place = \App\Models\Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return redirect()->route('dj-offers.dj-list')
                ->with('error', 'You need to have a place assigned to create offers.');
        }
        
        return Inertia::render('offers/Create', [
            'dj' => $dj->load('djApplications'),
            'place' => $place
        ]);
    }
    
    /**
     * Store DJ offer
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can create offers.');
        }
        
        $validated = $request->validate([
            'dj_id' => 'required|exists:users,id',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required',
            'duration' => 'required|numeric|min:0.5|max:24',
            'budget' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        // Get place manager's place
        $place = \App\Models\Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return back()->withErrors(['place' => 'You need to have a place assigned to create offers.']);
        }
        
        // Check if DJ has approved application
        $djUser = User::find($validated['dj_id']);
        if (!$djUser->hasRole('dj')) {
            return back()->withErrors(['dj_id' => 'Selected user is not a DJ.']);
        }
        
        $offer = DjOffer::create([
            'place_manager_id' => $user->id,
            'dj_id' => $validated['dj_id'],
            'place_id' => $place->id,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'duration' => $validated['duration'],
            'budget' => $validated['budget'],
            'description' => $validated['description'],
            'expires_at' => $validated['expires_at'] ?? now()->addDays(7), // Default 7 days
        ]);
        
        return redirect()->route('dj-offers.index')
            ->with('success', 'DJ offer sent successfully!');
    }
    
    /**
     * Display offers list (for both place managers and DJs)
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('placeManager')) {
            // Show offers sent by this place manager
            $offers = DjOffer::where('place_manager_id', $user->id)
                ->with(['dj', 'place', 'messages'])
                ->withCount(['messages as unread_messages_count' => function($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)->where('is_read', false);
                }])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return Inertia::render('offers/Index', [
                'offers' => $offers
            ]);
            
        } elseif ($user->hasRole('dj')) {
            // Show offers received by this DJ
            $offers = DjOffer::where('dj_id', $user->id)
                ->with(['placeManager', 'place', 'messages'])
                ->withCount(['messages as unread_messages_count' => function($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)->where('is_read', false);
                }])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return Inertia::render('offers/Index', [
                'offers' => $offers
            ]);
        }
        
        abort(403, 'Only place managers and DJs can access offers.');
    }
    
    /**
     * Show single offer with messages
     */
    public function show(DjOffer $offer)
    {
        $user = Auth::user();
        
        // Check if user has access to this offer
        if ($offer->place_manager_id !== $user->id && $offer->dj_id !== $user->id) {
            abort(403, 'You do not have access to this offer.');
        }
        
        $offer->load(['placeManager', 'dj', 'place', 'messages.user']);
        
        // Mark messages as read for current user
        $offer->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return Inertia::render('offers/Show', [
            'offer' => $offer,
            'messages' => $offer->messages,
            'canRespond' => $user->hasRole('dj') && $offer->isPending() && !$offer->isExpired()
        ]);
    }
    
    /**
     * Accept offer (DJ only)
     */
    public function accept(DjOffer $offer)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('dj') || $offer->dj_id !== $user->id) {
            abort(403, 'Only the DJ can accept this offer.');
        }
        
        if (!$offer->isPending()) {
            return back()->withErrors(['status' => 'This offer has already been responded to.']);
        }
        
        if ($offer->isExpired()) {
            return back()->withErrors(['expired' => 'This offer has expired.']);
        }
        
        $offer->accept();
        
        return redirect()->route('dj-offers.show', $offer)
            ->with('success', 'Offer accepted successfully!');
    }
    
    /**
     * Reject offer (DJ only)
     */
    public function reject(DjOffer $offer)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('dj') || $offer->dj_id !== $user->id) {
            abort(403, 'Only the DJ can reject this offer.');
        }
        
        if (!$offer->isPending()) {
            return back()->withErrors(['status' => 'This offer has already been responded to.']);
        }
        
        $offer->reject();
        
        return redirect()->route('dj-offers.show', $offer)
            ->with('success', 'Offer rejected.');
    }
    
    /**
     * Cancel offer (Place Manager only)
     */
    public function cancel(DjOffer $offer)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager') || $offer->place_manager_id !== $user->id) {
            abort(403, 'Only the place manager can cancel this offer.');
        }
        
        if ($offer->isAccepted()) {
            return back()->withErrors(['status' => 'Cannot cancel an accepted offer.']);
        }
        
        $offer->cancel();
        
        return redirect()->route('dj-offers.show', $offer)
            ->with('success', 'Offer cancelled successfully.');
    }
}
