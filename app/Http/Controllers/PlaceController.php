<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $places = Place::paginate(10);
        return Inertia::render('places/Index', [
            'places' => $places
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can create places.');
        }
        return Inertia::render('places/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can create places.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $place = Place::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $manager = ImageManager::gd();
                $image = $manager->read($imageFile)->toWebp(90);
                $imageName = uniqid('place_') . '.webp';
                $imagePath = 'places/' . $imageName;
                Storage::disk('public')->put($imagePath, $image);
                $place->images()->create(['path' => $imagePath]);
            }
        }

        return redirect()->route('places.index')->with('success', 'Place created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $place = Place::findOrFail($id);
        return Inertia::render('places/Show', [
            'place' => $place
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $place = Place::findOrFail($id);
        return Inertia::render('places/Edit', [
            'place' => $place
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can update places.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $place = Place::findOrFail($id);
        $place->update($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $manager = ImageManager::gd();
                $image = $manager->read($imageFile)->toWebp(90);
                $imageName = uniqid('place_') . '.webp';
                $imagePath = 'places/' . $imageName;
                Storage::disk('public')->put($imagePath, $image);
                $place->images()->create(['path' => $imagePath]);
            }
        }

        return redirect()->route('places.index')->with('success', 'Place updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can delete places.');
        }

        $place = Place::findOrFail($id);
        $place->delete();

        return redirect()->route('places.index')->with('success', 'Place deleted successfully.');
    }
}
