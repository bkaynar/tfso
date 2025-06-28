<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Track::with(['user', 'category']);

        // If user is DJ, only show their own tracks
        $user = Auth::user();
        if ($user && $user->hasRole('dj') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $tracks = $query->latest()->paginate(10);

        return Inertia::render('tracks/Index', [
            'tracks' => $tracks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        // If user is DJ, only allow them to create tracks for themselves
        $user = Auth::user();
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            $users = collect([$user]); // Only current user
        } else {
            $users = User::all(); // All users for admin
        }

        return Inertia::render('tracks/Create', [
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Increase timeout for large file uploads
        set_time_limit(300); // 5 minutes

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
            'audio_file' => 'required|mimes:mp3,wav,ogg,m4a|max:204800', // 200MB max
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'duration' => 'nullable|string|max:10',
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Authorization check for DJ users
        $user = Auth::user();
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            // DJ can only create tracks for themselves
            if ($request->user_id != $user->id) {
                abort(403, 'DJ users can only create tracks for themselves.');
            }
        }

        $data = $request->only(['title', 'description', 'category_id', 'user_id', 'duration', 'is_premium', 'iap_product_id']);

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('tracks/audio', 'public');
            $data['audio_file'] = $audioFilePath;
        }

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            $imageFile = $request->file('image_file');
            $imageFilePath = $imageFile->store('tracks/images', 'public');
            $data['image_file'] = $imageFilePath;
        }

        Track::create($data);

        return redirect()->route('tracks.index')->with('success', 'Track created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        $track->load(['user', 'category']);

        return Inertia::render('tracks/Show', [
            'track' => $track
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Track $track)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $track->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to edit this track.');
        }

        $categories = Category::all();

        // If user is DJ, only allow them to select themselves
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            $users = collect([$user]); // Only current user
        } else {
            $users = User::all(); // All users for admin
        }

        return Inertia::render('tracks/Edit', [
            'track' => $track,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Track $track)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $track->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to update this track.');
        }

        // Increase timeout for large file uploads
        set_time_limit(300); // 5 minutes

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'user_id' => 'nullable|exists:users,id',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:204800', // 200MB max
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'duration' => 'nullable|string|max:10',
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Additional authorization check for DJ users
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            // DJ can only update tracks for themselves
            if ($request->user_id != $user->id) {
                abort(403, 'DJ users can only update tracks for themselves.');
            }
        }

        $data = $request->only(['title', 'description', 'category_id', 'user_id', 'duration', 'is_premium', 'iap_product_id']);

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            // Delete old audio file
            if ($track->audio_file) {
                Storage::disk('public')->delete($track->audio_file);
            }

            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('tracks/audio', 'public');
            $data['audio_file'] = $audioFilePath;
        }

        // Handle image file upload
        if ($request->hasFile('image_file')) {
            // Delete old image file
            if ($track->image_file) {
                Storage::disk('public')->delete($track->image_file);
            }

            $imageFile = $request->file('image_file');
            $imageFilePath = $imageFile->store('tracks/images', 'public');
            $data['image_file'] = $imageFilePath;
        }

        $track->update($data);

        return redirect()->route('tracks.index')->with('success', 'Track updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Track $track)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $track->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to delete this track.');
        }

        // Delete associated files
        if ($track->audio_file) {
            Storage::disk('public')->delete($track->audio_file);
        }

        if ($track->image_file) {
            Storage::disk('public')->delete($track->image_file);
        }

        $track->delete();

        return redirect()->route('tracks.index')->with('success', 'Track deleted successfully.');
    }
}
