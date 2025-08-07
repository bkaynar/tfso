<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:dj,placeManager',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // If role is DJ, don't assign role yet - they need to complete application
        if ($request->role === 'dj') {
            event(new Registered($user));
            Auth::login($user);
            
            // Redirect to DJ application form
            return to_route('dj.application.create')->with('info', 'Please complete your DJ application to access the platform.');
        } 
        // If role is placeManager, assign role and redirect to place creation
        else {
            $user->assignRole($request->role);
            
            // Refresh the user model to ensure role is loaded
            $user->refresh();
            
            event(new Registered($user));
            Auth::login($user);
            
            // Clear any cached permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            // Redirect to place creation for placeManager
            return to_route('placemanager.place.create')->with('info', 'Welcome! Please create your place to complete your profile.');
        }
    }
}
