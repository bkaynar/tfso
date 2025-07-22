<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $query = Event::with(['user']);

        $user = Auth::user();
        if ($user && $user->hasRole('dj') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $events = $query->latest()->paginate(10);

        return inertia('events/Index', [
            'events' => $events
        ]);
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'dj');
        })->with('roles')->get();

        return inertia('events/Create', [
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'ticket_url' => 'nullable|url',
            'photo' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
            'cover_image' => 'nullable|file|image|max:2048',
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string',
        ]);

        // Admin değilse kendi ID'sini kullan
        if (!Auth::user()->hasRole('admin')) {
            $validated['user_id'] = Auth::id();
        }

        // Cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event = Event::create($validated);
        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $event->load(['user']);
        return inertia('events/Show', [
            'event' => $event
        ]);
    }

    public function edit(Event $event)
    {
        $event->load(['user']);

        $categories = \App\Models\Category::all();
        $users = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'dj');
        })->with('roles')->get();

        return inertia('events/Edit', [
            'event' => $event,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'ticket_url' => 'nullable|url',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
            'cover_image' => 'nullable|file|image|max:2048',
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string',
        ]);

        // Admin değilse sadece kendi event'lerini düzenleyebilir
        if (!Auth::user()->hasRole('admin') && $event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cover image upload
        if ($request->hasFile('cover_image')) {
            // Eski resmi sil
            if ($event->cover_image) {
                \Storage::disk('public')->delete($event->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event->update($validated);
        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
