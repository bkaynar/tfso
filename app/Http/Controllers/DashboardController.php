<?php

namespace App\Http\Controllers;

use App\Models\DjApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $djApplication = null;
        
        // Check if user has a DJ application (and no DJ role yet)
        if (!$user->hasRole('dj') && !$user->hasRole('admin')) {
            $djApplication = DjApplication::where('email', $user->email)->first();
        }
        
        return Inertia::render('Dashboard', [
            'djApplication' => $djApplication
        ]);
    }
}
