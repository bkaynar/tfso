<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Set::with(['user', 'category']);

        // If user is DJ, only show their own sets
        $user = Auth::user();
        if ($user && $user->hasRole('dj') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $sets = $query->latest()->paginate(10);

        return Inertia::render('sets/Index', [
            'sets' => $sets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        // If user is DJ, only allow them to create sets for themselves
        $user = Auth::user();
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            $users = collect([$user]); // Only current user
        } else {
            $users = \App\Models\User::all(); // All users for admin
        }

        return Inertia::render('sets/Create', [
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:204800', // 200MB max
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Authorization check for DJ users
        $user = Auth::user();
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            // DJ can only create sets for themselves
            if ($request->user_id != $user->id) {
                abort(403, 'DJ users can only create sets for themselves.');
            }
        }

        $data = $request->only(['name', 'description', 'category_id', 'user_id', 'is_premium', 'iap_product_id']);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $coverImagePath = $coverImage->store('sets/covers', 'public');
            $data['cover_image'] = Storage::url($coverImagePath);
        }

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('sets/audio', 'public');
            $data['audio_file'] = Storage::url($audioFilePath);
        }

        Set::create($data);

        return redirect()->route('sets.index')->with('success', 'Set created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Set $set)
    {
        $set->load(['user', 'category', 'tracks']);

        return Inertia::render('sets/Show', [
            'set' => $set
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Set $set)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $set->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to edit this set.');
        }

        $categories = Category::all();

        // If user is DJ, only allow them to select themselves
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            $users = collect([$user]); // Only current user
        } else {
            $users = \App\Models\User::all(); // All users for admin
        }

        return Inertia::render('sets/Edit', [
            'set' => $set,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Set $set)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $set->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to update this set.');
        }

        // Increase timeout for large file uploads
        set_time_limit(300); // 5 minutes

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:204800', // 200MB max
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Additional authorization check for DJ users
        if ($user->hasRole('dj') && !$user->hasRole('admin')) {
            // DJ can only update sets for themselves
            if ($request->user_id != $user->id) {
                abort(403, 'DJ users can only update sets for themselves.');
            }
        }

        $data = $request->only(['name', 'description', 'category_id', 'user_id', 'is_premium', 'iap_product_id']);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image
            if ($set->cover_image) {
                $oldImagePath = str_replace('/storage/', '', $set->cover_image);
                Storage::disk('public')->delete($oldImagePath);
            }

            $coverImage = $request->file('cover_image');
            $coverImagePath = $coverImage->store('sets/covers', 'public');
            $data['cover_image'] = Storage::url($coverImagePath);
        }

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            // Delete old audio file
            if ($set->audio_file) {
                $oldAudioPath = str_replace('/storage/', '', $set->audio_file);
                Storage::disk('public')->delete($oldAudioPath);
            }

            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('sets/audio', 'public');
            $data['audio_file'] = Storage::url($audioFilePath);
        }

        $set->update($data);

        return redirect()->route('sets.index')->with('success', 'Set updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Set $set)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user->hasRole('admin') && (!$user->hasRole('dj') || $set->user_id !== $user->id)) {
            abort(403, 'Unauthorized access to delete this set.');
        }

        // Delete associated files
        if ($set->cover_image) {
            $coverImagePath = str_replace('/storage/', '', $set->cover_image);
            Storage::disk('public')->delete($coverImagePath);
        }

        if ($set->audio_file) {
            $audioFilePath = str_replace('/storage/', '', $set->audio_file);
            Storage::disk('public')->delete($audioFilePath);
        }

        $set->delete();

        return redirect()->route('sets.index')->with('success', 'Set deleted successfully.');
    }
}
