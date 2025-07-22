<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $query = Event::with(['user', 'category']);

        $user = Auth::user();
        if ($user && $user->hasRole('dj') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $events = $query->latest()->paginate(10);

        return inertia('events/Index', [
            'events' => $events
        ]);
    }
}
