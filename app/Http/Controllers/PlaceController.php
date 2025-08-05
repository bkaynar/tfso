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
        $places = Place::with('images')->paginate(10);
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
            'phone' => 'nullable|string|max:20',
            'google_maps_url' => 'nullable|url',
            'apple_maps_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'is_premium' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Remove images from validation data
        $placeData = \Illuminate\Support\Arr::except($validated, ['images']);
        $place = Place::create($placeData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $manager = ImageManager::gd();
                $image = $manager->read($imageFile);

                // Use PNG for now instead of WebP to debug the issue
                $imageName = uniqid('place_') . '.png';
                $imagePath = 'places/' . $imageName;
                Storage::disk('public')->put($imagePath, $image->toPng());

                $place->images()->create(['path' => $imagePath]);
            }
        }

        return redirect()->route('places.index')->with('success', 'Place created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $place = Place::with('images')->findOrFail($id);
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
            'phone' => 'nullable|string|max:20',
            'google_maps_url' => 'nullable|url',
            'apple_maps_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'is_premium' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_images' => 'nullable|string', // JSON string of existing images to keep
        ]);

        $place = Place::findOrFail($id);

        // Remove images from validation data
        $placeData = \Illuminate\Support\Arr::except($validated, ['images', 'existing_images']);
        $place->update($placeData);

        // Handle existing images removal (remove images not in the existing_images list)
        if ($request->has('existing_images')) {
            $existingImagesToKeep = json_decode($request->existing_images, true) ?: [];
            $currentImages = $place->images;

            foreach ($currentImages as $currentImage) {
                $shouldKeep = false;
                foreach ($existingImagesToKeep as $keepImage) {
                    if ((is_string($keepImage) && $currentImage->path === $keepImage) ||
                        (is_array($keepImage) && isset($keepImage['path']) && $currentImage->path === $keepImage['path']) ||
                        (is_object($keepImage) && isset($keepImage->path) && $currentImage->path === $keepImage->path)
                    ) {
                        $shouldKeep = true;
                        break;
                    }
                }

                if (!$shouldKeep) {
                    Storage::disk('public')->delete($currentImage->path);
                    $currentImage->delete();
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $manager = ImageManager::gd();
                $image = $manager->read($imageFile);
                $imageName = uniqid('place_') . '.png';
                $imagePath = 'places/' . $imageName;
                Storage::disk('public')->put($imagePath, $image->toPng());
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

        // Delete associated images from storage
        foreach ($place->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $place->delete();

        return redirect()->route('places.index')->with('success', 'Place deleted successfully.');
    }
}
