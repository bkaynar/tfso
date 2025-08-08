<?php

namespace App\Http\Controllers;

use App\Models\DjApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DjApplicationController extends Controller
{
    /**
     * Show DJ application form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user already has a pending or approved application
        $existingApplication = DjApplication::where('email', $user->email)->first();
        
        if ($existingApplication) {
            if ($existingApplication->isApproved()) {
                return redirect()->route('dashboard')->with('info', 'Your DJ application has already been approved.');
            } elseif ($existingApplication->isPending()) {
                return Inertia::render('dj-applications/Status', [
                    'application' => $existingApplication
                ]);
            }
        }
        
        return Inertia::render('dj-applications/Create');
    }

    /**
     * Store DJ application
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'intention_letter' => 'required|string|min:50|max:2000',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
        ]);

        // Check if user already has an application
        $existingApplication = DjApplication::where('email', $user->email)->first();
        if ($existingApplication) {
            return back()->withErrors(['email' => 'You have already submitted a DJ application.']);
        }

        DjApplication::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $validated['phone'],
            'password' => $user->password, // Copy existing password
            'intention_letter' => $validated['intention_letter'],
            'facebook' => $validated['facebook'],
            'instagram' => $validated['instagram'],
            'twitter' => $validated['twitter'],
            'youtube' => $validated['youtube'],
            'status' => 'pending',
        ]);

        return redirect()->route('dj.application.status')->with('success', 'Your DJ application has been submitted successfully! You will be notified when it is reviewed.');
    }

    /**
     * Show application status
     */
    public function status()
    {
        $user = Auth::user();
        $application = DjApplication::where('email', $user->email)->first();
        
        if (!$application) {
            return redirect()->route('dj.application.create')->with('info', 'Please complete your DJ application.');
        }
        
        return Inertia::render('dj-applications/Status', [
            'application' => $application->load('approvedBy')
        ]);
    }

    /**
     * Admin: List all DJ applications
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can access DJ applications.');
        }

        $applications = DjApplication::with('approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingCount = DjApplication::where('status', 'pending')->count();

        return Inertia::render('admin/dj-applications/Index', [
            'applications' => $applications,
            'pendingCount' => $pendingCount
        ]);
    }

    /**
     * Admin: Show single DJ application
     */
    public function show(DjApplication $djApplication)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can view DJ applications.');
        }

        return Inertia::render('admin/dj-applications/Show', [
            'application' => $djApplication->load('approvedBy')
        ]);
    }

    /**
     * Admin: Approve DJ application
     */
    public function approve(Request $request, DjApplication $djApplication)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can approve DJ applications.');
        }

        $validated = $request->validate([
            'admin_comment' => 'nullable|string|max:500',
        ]);

        if (!$djApplication->isPending()) {
            return back()->withErrors(['status' => 'This application has already been processed.']);
        }

        // Find existing user and assign DJ role
        $djUser = User::where('email', $djApplication->email)->first();
        if (!$djUser) {
            return back()->withErrors(['email' => 'User not found in system.']);
        }

        $djUser->assignRole('dj');

        // Approve the application
        $djApplication->approve($user, $validated['admin_comment']);

        return redirect()->route('admin.dj-applications.index')
            ->with('success', 'DJ application approved successfully. User account has been created.');
    }

    /**
     * Admin: Reject DJ application
     */
    public function reject(Request $request, DjApplication $djApplication)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Only administrators can reject DJ applications.');
        }

        $validated = $request->validate([
            'admin_comment' => 'required|string|max:500',
        ]);

        if (!$djApplication->isPending()) {
            return back()->withErrors(['status' => 'This application has already been processed.']);
        }

        // Reject the application
        $djApplication->reject($user, $validated['admin_comment']);

        return redirect()->route('admin.dj-applications.index')
            ->with('success', 'DJ application rejected successfully.');
    }
}
