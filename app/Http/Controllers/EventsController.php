<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // If user is admin or dj, show all events
        if ($user->hasRole('admin') || $user->hasRole('dj')) {
            $events = Events::with(['user', 'place'])->paginate(10);
        } 
        // If user is placeManager, show only events for their places
        elseif ($user->hasRole('placeManager')) {
            $userPlaceIds = $user->places->pluck('id');
            $events = Events::with(['user', 'place'])
                ->whereIn('place_id', $userPlaceIds)
                ->paginate(10);
        } 
        else {
            abort(403, 'Unauthorized access to events.');
        }
        
        return Inertia::render('events/Index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $users = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'dj');
        })->get(['id', 'name']);
        
        // If user is admin or dj, show all places
        if ($user->hasRole('admin') || $user->hasRole('dj')) {
            $places = \App\Models\Place::get(['id', 'name']);
        } 
        // If user is placeManager, show only their places
        elseif ($user->hasRole('placeManager')) {
            $places = $user->places()->get(['id', 'name']);
        } 
        else {
            abort(403, 'Unauthorized access to create events.');
        }
        
        return Inertia::render('events/Create', [
            'users' => $users,
            'places' => $places
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'place_id' => 'nullable|exists:places,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ticket_link' => 'nullable|string|max:255',
        ]);

        // If placeManager, validate they can only create events for their places
        if ($user->hasRole('placeManager') && $validated['place_id']) {
            $userPlaceIds = $user->places->pluck('id')->toArray();
            if (!in_array($validated['place_id'], $userPlaceIds)) {
                abort(403, 'You can only create events for your own places.');
            }
        }

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $manager = ImageManager::gd();
            $image = $manager->read($imageFile)->toWebp(90);
            $imageName = uniqid('event_') . '.webp';
            $imagePath = 'events/' . $imageName;
            Storage::disk('public')->put($imagePath, $image);
            $validated['image'] = $imagePath;
        }
        
        Events::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Events $event)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // If placeManager, check if this event belongs to their place
        if ($user->hasRole('placeManager')) {
            $userPlaceIds = $user->places->pluck('id')->toArray();
            if ($event->place_id && !in_array($event->place_id, $userPlaceIds)) {
                abort(403, 'You can only edit events for your own places.');
            }
        }
        
        $users = \App\Models\User::whereHas('roles', function($query) {
            $query->where('name', 'dj');
        })->get(['id', 'name']);
        
        // If user is admin or dj, show all places
        if ($user->hasRole('admin') || $user->hasRole('dj')) {
            $places = \App\Models\Place::get(['id', 'name']);
        } 
        // If user is placeManager, show only their places
        elseif ($user->hasRole('placeManager')) {
            $places = $user->places()->get(['id', 'name']);
        } 
        else {
            abort(403, 'Unauthorized access to edit events.');
        }
        
        return Inertia::render('events/Edit', [
            'event' => $event,
            'users' => $users,
            'places' => $places
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Events $event)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // If placeManager, check if this event belongs to their place
        if ($user->hasRole('placeManager')) {
            $userPlaceIds = $user->places->pluck('id')->toArray();
            if ($event->place_id && !in_array($event->place_id, $userPlaceIds)) {
                abort(403, 'You can only update events for your own places.');
            }
        }
        
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'place_id' => 'nullable|exists:places,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ticket_link' => 'nullable|string|max:255',
        ]);

        // If placeManager, validate they can only update to their places
        if ($user->hasRole('placeManager') && $validated['place_id']) {
            $userPlaceIds = $user->places->pluck('id')->toArray();
            if (!in_array($validated['place_id'], $userPlaceIds)) {
                abort(403, 'You can only update events to your own places.');
            }
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            
            $imageFile = $request->file('image');
            $manager = ImageManager::gd();
            $image = $manager->read($imageFile)->toWebp(90);
            $imageName = uniqid('event_') . '.webp';
            $imagePath = 'events/' . $imageName;
            Storage::disk('public')->put($imagePath, $image);
            $validated['image'] = $imagePath;
        }
        
        $event->update($validated);
        
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Events $event)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // If placeManager, check if this event belongs to their place
        if ($user->hasRole('placeManager')) {
            $userPlaceIds = $user->places->pluck('id')->toArray();
            if ($event->place_id && !in_array($event->place_id, $userPlaceIds)) {
                abort(403, 'You can only delete events for your own places.');
            }
        }
        
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
