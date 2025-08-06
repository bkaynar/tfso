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
        $events = Events::paginate(10);
        return Inertia::render('events/Index', [
            'events' => $events
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('events/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'ticket_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $manager = ImageManager::gd();
            $image = $manager->read($imageFile)->toWebp(90);
            $imageName = uniqid('category_') . '.webp';
            $imagePath = 'categories/' . $imageName;
            Storage::disk('public')->put($imagePath, $image);
            $data['image'] = $imagePath;
        }
        $event = Events::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Events $events)
    {
        return Inertia::render('events/Edit', [
            'event' => $events
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Events $events)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'ticket_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $manager = ImageManager::gd();
            $image = $manager->read($imageFile)->toWebp(90);
            $imageName = uniqid('category_') . '.webp';
            $imagePath = 'categories/' . $imageName;
            Storage::disk('public')->put($imagePath, $image);
            $validated['image'] = $imagePath;
        }
        $events->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Events $events)
    {
        if ($events->image) {
            Storage::disk('public')->delete($events->image);
        }
        $events->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
