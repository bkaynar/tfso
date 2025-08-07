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
        $user = Auth::user();
        
        // Only admin can access places index
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can access places listing.');
        }
        
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
        if (!$user->hasRole('admin') && !$user->hasRole('placeManager')) {
            abort(403, 'Only administrators and place managers can create places.');
        }
        
        // If admin, provide list of placeManager users
        $placeManagers = [];
        if ($user->hasRole('admin')) {
            $placeManagers = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'placeManager');
            })->get(['id', 'name']);
        }
        
        return Inertia::render('places/Create', [
            'placeManagers' => $placeManagers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('placeManager')) {
            abort(403, 'Only administrators and place managers can create places.');
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
        
        // If placeManager, set user_id to current user
        if ($user->hasRole('placeManager')) {
            $placeData['user_id'] = $user->id;
        }
        // If admin, allow setting user_id if provided, otherwise null
        elseif ($user->hasRole('admin') && $request->has('user_id')) {
            $placeData['user_id'] = $request->user_id;
        }
        
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
        $user = Auth::user();
        $place = Place::with('images')->findOrFail($id);
        
        // If placeManager, check if they own this place
        if ($user->hasRole('placeManager') && $place->user_id !== $user->id) {
            abort(403, 'You can only edit your own places.');
        }
        
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
        $place = Place::findOrFail($id);
        
        // Check permissions
        if (!$user->hasRole('admin') && !$user->hasRole('placeManager')) {
            abort(403, 'Only administrators and place managers can update places.');
        }
        
        // If placeManager, check if they own this place
        if ($user->hasRole('placeManager') && $place->user_id !== $user->id) {
            abort(403, 'You can only update your own places.');
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
        $place = Place::findOrFail($id);
        
        // Check permissions
        if (!$user->hasRole('admin') && !$user->hasRole('placeManager')) {
            abort(403, 'Only administrators and place managers can delete places.');
        }
        
        // If placeManager, check if they own this place
        if ($user->hasRole('placeManager') && $place->user_id !== $user->id) {
            abort(403, 'You can only delete your own places.');
        }

        // Delete associated images from storage
        foreach ($place->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $place->delete();

        return redirect()->route('places.index')->with('success', 'Place deleted successfully.');
    }
    
    /**
     * Show the form for creating a new place specifically for new placeManagers
     */
    public function createForPlaceManager()
    {
        $user = Auth::user();
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can access this page.');
        }
        
        // Check if user already has a place
        $existingPlace = Place::where('user_id', $user->id)->first();
        if ($existingPlace) {
            return redirect()->route('places.index')->with('info', 'You already have a place registered.');
        }
        
        return Inertia::render('placemanager/CreatePlace');
    }
    
    /**
     * Store a newly created place for new placeManagers
     */
    public function storeForPlaceManager(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can create places through this method.');
        }
        
        // Check if user already has a place
        $existingPlace = Place::where('user_id', $user->id)->first();
        if ($existingPlace) {
            return redirect()->route('places.index')->with('info', 'You already have a place registered.');
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Remove images from validation data and set user_id
        $placeData = \Illuminate\Support\Arr::except($validated, ['images']);
        $placeData['user_id'] = $user->id;
        $placeData['is_premium'] = false; // Default to non-premium for new placeManagers
        
        $place = Place::create($placeData);

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

        return redirect()->route('dashboard')->with('success', 'Your place has been created successfully! Welcome to the platform.');
    }
    
    /**
     * PlaceManager'ın kendi mekanını düzenlemesi için doğrudan yönlendirme
     */
    public function editMyPlace()
    {
        $user = Auth::user();
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can access this page.');
        }
        
        // PlaceManager'ın mekanını bul
        $place = Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return redirect()->route('placemanager.place.create')->with('info', 'Please create your place first.');
        }
        
        // Normal edit sayfasına yönlendir
        return redirect()->route('places.edit', $place->id);
    }
}
