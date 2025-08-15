<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Role filter
        if ($request->has('role') && $request->role !== '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search filter
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->appends($request->all());

        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => [
                'role' => $request->role ?? '',
                'search' => $request->search ?? '',
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();

        return Inertia::render('users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'bio' => 'nullable|string|max:5000', // Text alan için 5000 karakter limit
            'location' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'soundcloud' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'iap_product_id' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $data = $request->only([
            'name',
            'email',
            'bio',
            'location',
            'instagram',
            'twitter',
            'facebook',
            'youtube',
            'soundcloud',
            'iap_product_id'
        ]);

        $data['password'] = bcrypt($request->password);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
            $manager = ImageManager::gd();
            $image = $manager->read($profilePhoto)->toWebp(90);
            $imageName = uniqid('user_profile_') . '.webp';
            $profilePhotoPath = 'users/profiles/' . $imageName;
            Storage::disk('public')->put($profilePhotoPath, (string) $image);
            $data['profile_photo'] = $profilePhotoPath;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $manager = ImageManager::gd();
            $image = $manager->read($coverImage)->toWebp(90);
            $imageName = uniqid('user_cover_') . '.webp';
            $coverImagePath = 'users/covers/' . $imageName;
            Storage::disk('public')->put($coverImagePath, (string) $image);
            $data['cover_image'] = $coverImagePath;
        }

        $user = User::create($data);

        // Assign roles to the user
        if ($request->has('roles') && is_array($request->roles)) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');

        return Inertia::render('users/Show', [
            'user' => $user
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = \Spatie\Permission\Models\Role::all();

        return Inertia::render('users/Edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Debug için log ekleyelim
        Log::info('Update request files:', [
            'has_profile_photo' => $request->hasFile('profile_photo'),
            'has_cover_image' => $request->hasFile('cover_image'),
            'all_files' => $request->allFiles(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'bio' => 'nullable|string|max:5000', // Text alan için 5000 karakter limit
            'location' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'soundcloud' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'iap_product_id' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $data = $request->only([
            'name',
            'email',
            'bio',
            'location',
            'instagram',
            'twitter',
            'facebook',
            'youtube',
            'soundcloud',
            'iap_product_id'
        ]);

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $profilePhoto = $request->file('profile_photo');
            $manager = ImageManager::gd();
            $image = $manager->read($profilePhoto)->toWebp(90);
            $imageName = uniqid('user_profile_') . '.webp';
            $profilePhotoPath = 'users/profiles/' . $imageName;
            Storage::disk('public')->put($profilePhotoPath, (string) $image);
            $data['profile_photo'] = $profilePhotoPath;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }

            $coverImage = $request->file('cover_image');
            $manager = ImageManager::gd();
            $image = $manager->read($coverImage)->toWebp(90);
            $imageName = uniqid('user_cover_') . '.webp';
            $coverImagePath = 'users/covers/' . $imageName;
            Storage::disk('public')->put($coverImagePath, (string) $image);
            $data['cover_image'] = $coverImagePath;
        }

        $user->update($data);

        // Update roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles ?? []);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Delete profile photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Delete cover image if exists
        if ($user->cover_image) {
            Storage::disk('public')->delete($user->cover_image);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for editing user's own profile.
     */
    public function editProfile()
    {
        $user = Auth::user();
        $user->load('roles');

        return Inertia::render('profile/EditOrCreate', [
            'user' => $user,
            'isOwnProfile' => true
        ]);
    }

    /**
     * Update user's own profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Debug için log ekleyelim
        Log::info('Profile update request (UserController):', [
            'has_profile_photo' => $request->hasFile('profile_photo'),
            'has_cover_image' => $request->hasFile('cover_image'),
            'all_files' => $request->allFiles(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'bio' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'soundcloud' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name',
            'email',
            'bio',
            'location',
            'instagram',
            'twitter',
            'facebook',
            'youtube',
            'soundcloud'
        ]);

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $profilePhoto = $request->file('profile_photo');
            $manager = ImageManager::gd();
            $image = $manager->read($profilePhoto)->toWebp(90);
            $imageName = uniqid('user_profile_') . '.webp';
            $profilePhotoPath = 'users/profiles/' . $imageName;
            Storage::disk('public')->put($profilePhotoPath, (string) $image);
            $data['profile_photo'] = $profilePhotoPath;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }

            $coverImage = $request->file('cover_image');
            $manager = ImageManager::gd();
            $image = $manager->read($coverImage)->toWebp(90);
            $imageName = uniqid('user_cover_') . '.webp';
            $coverImagePath = 'users/covers/' . $imageName;
            Storage::disk('public')->put($coverImagePath, (string) $image);
            $data['cover_image'] = $coverImagePath;
        }

        $user->update($data);

        Log::info('Profile update request data:', $request->all());

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mevcut şifre yanlış.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Şifreniz başarıyla güncellendi.');
    }

    /**
     * Show delete account page
     */
    public function showDeleteAccount()
    {
        return Inertia::render('delete-account/Index');
    }

    /**
     * Delete user account with email and password verification
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'confirm_deletion' => 'required|boolean|accepted',
        ], [
            'email.required' => 'E-posta adresini girmeniz gerekiyor.',
            'email.email' => 'Geçerli bir e-posta adresi girmeniz gerekiyor.',
            'password.required' => 'Şifrenizi girmeniz gerekiyor.',
            'confirm_deletion.accepted' => 'Hesap silme işlemini onaylamanız gerekiyor.',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Bu e-posta adresi ile kayıtlı bir hesap bulunamadı.']);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Şifre yanlış.']);
        }

        // Delete profile photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Delete cover image if exists
        if ($user->cover_image) {
            Storage::disk('public')->delete($user->cover_image);
        }

        // If user is currently logged in, log them out
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Delete the user account
        $user->delete();

        return redirect()->route('home')->with('success', 'Hesap başarıyla silindi.');
    }
}
