<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Set;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StageFeedController extends Controller
{
    public function index(Request $request)
    {
        try {
            $start = microtime(true);

            $page = (int) $request->get('page', 1);
            $limit = min((int) $request->get('limit', 20), 50);
            $includeWelcome = $request->boolean('include_welcome', true);

            $offset = ($page - 1) * $limit;
            $feedItems = collect();

            // Dinamik oranlar
            $setLimit = round($limit * 0.5);
            $trackLimit = round($limit * 0.3);
            $welcomeLimit = round($limit * 0.2);

            // SETS
            $sets = Set::select('id', 'user_id', 'name', 'cover_image', 'audio_file', 'description', 'created_at')
                ->with('user:id,name,profile_photo')
                ->withCount('likedByUsers')
                ->orderByDesc('created_at')
                ->skip($offset)
                ->take($setLimit)
                ->get();

            foreach ($sets as $set) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'set_' . $set->id,
                    'timestamp' => $set->created_at->timestamp,
                    'user' => [
                        'id' => $set->user->id,
                        'name' => $set->user->name,
                        'profile_photo' => $set->user->profile_photo_url
                    ],
                    'content' => [
                        'type' => 'set',
                        'title' => $set->name,
                        'image' => $set->cover_image ? url('storage/' . preg_replace('/^storage\//', '', ltrim($set->cover_image, '/'))) : null,
                        'audio_url' => $set->audio_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($set->audio_file, '/'))) : null,
                        'description' => $set->description,
                    ],
                    'created_at' => $set->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($set->created_at),
                    'likes_count' => $set->liked_by_users_count + 15,
                    'is_liked' => false,
                ]);
            }

            // TRACKS
            $tracks = Track::select('id', 'user_id', 'title', 'image_file', 'audio_file', 'created_at')
                ->with('user:id,name,profile_photo')
                ->withCount('likedByUsers')
                ->orderByDesc('created_at')
                ->skip($offset)
                ->take($trackLimit)
                ->get();

            foreach ($tracks as $track) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'track_' . $track->id,
                    'timestamp' => $track->created_at->timestamp,
                    'user' => [
                        'id' => $track->user->id,
                        'name' => $track->user->name,
                        'profile_photo' => $track->user->profile_photo_url
                    ],
                    'content' => [
                        'type' => 'track',
                        'title' => $track->title,
                        'image' => $track->image_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($track->image_file, '/'))) : null,
                        'audio_url' => $track->audio_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($track->audio_file, '/'))) : null,
                    ],
                    'created_at' => $track->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($track->created_at),
                    'likes_count' => $track->liked_by_users_count + 15,
                    'is_liked' => false,
                ]);
            }

            // WELCOME USERS
            if ($includeWelcome) {
                $newUsers = User::with('roles:name') // eager load roles
                    ->select('id', 'name', 'profile_photo', 'created_at')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->orderByDesc('created_at')
                    ->take($welcomeLimit)
                    ->get();

                foreach ($newUsers as $newUser) {
                    $userRole = $newUser->roles->pluck('name')->contains('dj') ? 'dj' : 'user';

                    $feedItems->push([
                        'type' => 'welcome',
                        'id' => 'welcome_' . $newUser->id,
                        'timestamp' => $newUser->created_at->timestamp,
                        'user' => [
                            'id' => $newUser->id,
                            'name' => $newUser->name,
                            'profile_photo' => $newUser->profile_photo_url,
                            'role' => $userRole,
                        ],
                        'joined_at' => $newUser->created_at->toISOString(),
                        'join_date' => $this->formatTimeAgo($newUser->created_at),
                        'follower_count' => 0,
                        'is_following' => false,
                    ]);
                }
            }

            // Final sorting by timestamp
            $feedItems = $feedItems->sortByDesc('timestamp')->values()->take($limit);

            $hasMore = $feedItems->count() >= $limit;
            $nextCursor = $hasMore ? base64_encode(json_encode(['page' => $page + 1])) : null;

            return response()->json([
                'success' => true,
                'data' => $feedItems,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'has_more' => $hasMore,
                    'next_cursor' => $nextCursor,
                ],
                'debug' => [
                    'duration' => round(microtime(true) - $start, 3) . 's',
                    'sets' => $sets->count(),
                    'tracks' => $tracks->count(),
                ],
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

        if ($diff->y > 0) return $diff->y . ' yıl önce';
        if ($diff->m > 0) return $diff->m . ' ay önce';
        if ($diff->d > 0) return $diff->d >= 7 ? floor($diff->d / 7) . ' hafta önce' : $diff->d . ' gün önce';
        if ($diff->h > 0) return $diff->h . ' saat önce';
        if ($diff->i > 0) return $diff->i . ' dakika önce';
        return 'şimdi';
    }
}
