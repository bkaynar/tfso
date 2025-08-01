<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;

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
            'audio_file' => 'nullable|mimes:mp3|max:204800', // Only MP3, 200MB max
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Additional validation for audio file
        if ($request->hasFile('audio_file')) {
            $this->validateAudioFile($request->file('audio_file'));
        }

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
            $manager = ImageManager::gd();
            $image = $manager->read($coverImage)->toWebp(90);
            $imageName = uniqid('set_cover_') . '.webp';
            $imagePath = 'sets/covers/' . $imageName;
            Storage::disk('public')->put($imagePath, (string) $image);
            $data['cover_image'] = Storage::url($imagePath);
        }

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('sets/audio', 'public');
            $data['audio_file'] = $audioFilePath; // Raw path, not URL
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
            'audio_file' => 'nullable|mimes:mp3|max:204800', // Only MP3, 200MB max
            'is_premium' => 'boolean',
            'iap_product_id' => 'nullable|string|max:255',
        ]);

        // Additional validation for audio file
        if ($request->hasFile('audio_file')) {
            $this->validateAudioFile($request->file('audio_file'));
        }

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
            $manager = ImageManager::gd();
            $image = $manager->read($coverImage)->toWebp(90);
            $imageName = uniqid('set_cover_') . '.webp';
            $imagePath = 'sets/covers/' . $imageName;
            Storage::disk('public')->put($imagePath, (string) $image);
            $data['cover_image'] = Storage::url($imagePath);
        }

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            // Delete old audio file
            if ($set->audio_file) {
                // If it's already a raw path, use it directly, otherwise clean the URL
                $oldAudioPath = str_contains($set->audio_file, '/storage/')
                    ? str_replace('/storage/', '', $set->audio_file)
                    : $set->audio_file;
                Storage::disk('public')->delete($oldAudioPath);
            }

            $audioFile = $request->file('audio_file');
            $audioFilePath = $audioFile->store('sets/audio', 'public');
            $data['audio_file'] = $audioFilePath; // Raw path, not URL
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
            // If it's already a raw path, use it directly, otherwise clean the URL
            $audioFilePath = str_contains($set->audio_file, '/storage/')
                ? str_replace('/storage/', '', $set->audio_file)
                : $set->audio_file;
            Storage::disk('public')->delete($audioFilePath);
        }

        $set->delete();

        return redirect()->route('sets.index')->with('success', 'Set deleted successfully.');
    }

    /**
     * Validate uploaded audio file for bitrate and duration
     */
    private function validateAudioFile($audioFile)
    {
        $filePath = $audioFile->getPathname();

        // Check if getid3 is available
        if (!class_exists('getID3')) {
            // If getid3 is not available, skip advanced validation
            return;
        }

        try {
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($filePath);

            // Check duration (maximum 90 minutes = 5400 seconds)
            if (isset($fileInfo['playtime_seconds'])) {
                $duration = $fileInfo['playtime_seconds'];
                if ($duration > 5400) {
                    throw ValidationException::withMessages([
                        'audio_file' => 'Audio file duration must not exceed 90 minutes.'
                    ]);
                }
            }

            // Check bitrate (192 kbps)
            if (isset($fileInfo['audio']['bitrate'])) {
                $bitrate = $fileInfo['audio']['bitrate'];
                // Allow some tolerance (±5 kbps)
                if ($bitrate < 187000 || $bitrate > 197000) {
                    throw ValidationException::withMessages([
                        'audio_file' => 'Audio file must be 192kbps bitrate.'
                    ]);
                }
            }

        } catch (\Exception $e) {
            // If analysis fails, log the error but don't block upload
            \Log::warning('Audio file validation failed: ' . $e->getMessage());
        }
    }
}
