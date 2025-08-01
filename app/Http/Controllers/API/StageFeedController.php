<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Set;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StageFeedController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $page = (int) $request->get('page', 1);
            $limit = min((int) $request->get('limit', 20), 50);
            $cursor = $request->get('cursor');
            $includeWelcome = $request->get('include_welcome', 'true') === 'true';

            $offset = ($page - 1) * $limit;
            $feedItems = collect();

            // Get recent sets and tracks for posts - optimized queries
            $sets = Set::select('id', 'user_id', 'name', 'cover_image', 'audio_file', 'description', 'created_at')
            ->with(['user:id,name,profile_photo'])
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(10) // Reduced from $limit * 0.7
            ->get();

            $tracks = Track::select('id', 'user_id', 'title', 'image_file', 'audio_file', 'created_at')
            ->with(['user:id,name,profile_photo'])
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(10) // Reduced from $limit * 0.7
            ->get();

            // Add sets as post items
            foreach ($sets as $set) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'set_' . $set->id,
                    'user' => [
                        'id' => $set->user->id,
                        'name' => $set->user->name,
                        'profile_photo' => $set->user->profile_photo ? url('/storage/' . $set->user->profile_photo) : null
                    ],
                    'content' => [
                        'type' => 'set',
                        'title' => $set->name,
                        'image' => $set->cover_image ? url('/storage/' . $set->cover_image) : null,
                        'audio_url' => $set->audio_file ? url('/storage/' . $set->audio_file) : null,
                        'duration' => 0,
                        'genre' => null,
                        'description' => $set->description ?? null
                    ],
                    'created_at' => $set->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($set->created_at),
                    'likes_count' => rand(10, 50),
                    'comments_count' => rand(0, 25),
                    'is_liked' => false
                ]);
            }

            // Add tracks as post items
            foreach ($tracks as $track) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'track_' . $track->id,
                    'user' => [
                        'id' => $track->user->id,
                        'name' => $track->user->name,
                        'profile_photo' => $track->user->profile_photo ? url('/storage/' . $track->user->profile_photo) : null
                    ],
                    'content' => [
                        'type' => 'track',
                        'title' => $track->title,
                        'image' => $track->image_file ? url('/storage/' . $track->image_file) : null,
                        'audio_url' => $track->audio_file ? url('/storage/' . $track->audio_file) : null,
                        'duration' => 0,
                        'genre' => null,
                        'description' => null
                    ],
                    'created_at' => $track->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($track->created_at),
                    'likes_count' => rand(10, 50),
                    'comments_count' => rand(0, 25),
                    'is_liked' => false
                ]);
            }

            // Add welcome items for new users (last 7 days)
            if ($includeWelcome) {
                // Get ALL new users from last 30 days (regardless of role) - optimized
                $allNewUsers = User::select('id', 'name', 'profile_photo', 'created_at')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->orderBy('created_at', 'desc')
                    ->take(5) // Reduced from $limit * 0.3
                    ->get();

                foreach ($allNewUsers as $newUser) {
                    // Check user roles more safely
                    $userRole = 'user'; // default
                    try {
                        if ($newUser->hasRole('dj')) {
                            $userRole = 'dj';
                        }
                    } catch (\Exception $e) {
                        // If role check fails, keep as 'user'
                        $userRole = 'user';
                    }
                    
                    $feedItems->push([
                        'type' => 'welcome',
                        'id' => 'welcome_' . $newUser->id,
                        'user' => [
                            'id' => $newUser->id,
                            'name' => $newUser->name,
                            'profile_photo' => $newUser->profile_photo ? url('/storage/' . $newUser->profile_photo) : null,
                            'role' => $userRole
                        ],
                        'joined_at' => $newUser->created_at->toISOString(),
                        'join_date' => $this->formatTimeAgo($newUser->created_at),
                        'follower_count' => 0,
                        'is_following' => false
                    ]);
                }
            }

            // Sort all items chronologically by created_at/joined_at
            $feedItems = $feedItems->sortByDesc(function ($item) {
                if ($item['type'] === 'welcome') {
                    return Carbon::parse($item['joined_at'])->timestamp;
                }
                return Carbon::parse($item['created_at'])->timestamp;
            })->values()->take($limit);

            // Generate next cursor
            $hasMore = $feedItems->count() >= $limit;
            $nextCursor = $hasMore ? base64_encode(json_encode(['page' => $page + 1])) : null;

            return response()->json([
                'success' => true,
                'data' => $feedItems->toArray(),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'has_more' => $hasMore,
                    'next_cursor' => $nextCursor
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FEED_ERROR',
                    'message' => 'Feed yüklenirken hata oluştu',
                    'debug' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function formatTimeAgo($date)
    {
        $diff = Carbon::now()->diff($date);

        if ($diff->y > 0) {
            return $diff->y . ' yıl önce';
        } elseif ($diff->m > 0) {
            return $diff->m . ' ay önce';
        } elseif ($diff->d > 0) {
            if ($diff->d >= 7) {
                $weeks = floor($diff->d / 7);
                return $weeks . ' hafta önce';
            }
            return $diff->d . ' gün önce';
        } elseif ($diff->h > 0) {
            return $diff->h . ' saat önce';
        } elseif ($diff->i > 0) {
            return $diff->i . ' dakika önce';
        } else {
            return 'şimdi';
        }
    }
}