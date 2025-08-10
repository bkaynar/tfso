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
            $user = auth('sanctum')->user();

            $page = (int) $request->get('page', 1);
            $limit = min((int) $request->get('limit', 20), 50);

            $feedItems = collect();

            // Posts için basit pagination
            $offset = ($page - 1) * $limit;

            // SETS
            $sets = Set::select('id', 'user_id', 'name', 'cover_image', 'audio_file', 'description', 'is_premium', 'created_at')
                ->with('user:id,name,profile_photo')
                ->withCount('likedByUsers')
                ->when($user, function($query) use ($user) {
                    return $query->with(['likedByUsers' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }]);
                })
                ->orderByDesc('created_at')
                ->get();

            foreach ($sets as $set) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'set_' . $set->id,
                    'timestamp' => $set->created_at->timestamp,
                    'user' => [
                        'id' => $set->user->id,
                        'name' => $set->user->name,
                        'profile_photo' => $set->user->profile_photo_url,
                        'is_following' => $user ? $user->favoriteDJs()->where('favorited_user_id', $set->user->id)->exists() : false
                    ],
                    'content' => [
                        'type' => 'set',
                        'title' => $set->name,
                        'image' => $set->cover_image ? url('storage/' . preg_replace('/^storage\//', '', ltrim($set->cover_image, '/'))) : null,
                        'audio_url' => $set->audio_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($set->audio_file, '/'))) : null,
                        'description' => $set->description,
                        'is_premium' => (bool) $set->is_premium,
                    ],
                    'created_at' => $set->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($set->created_at),
                    'likes_count' => $set->liked_by_users_count + 15,
                    'is_liked' => $user ? $set->likedByUsers->contains($user->id) : false,
                ]);
            }

            // TRACKS
            $tracks = Track::select('id', 'user_id', 'title', 'image_file', 'audio_file', 'is_premium', 'created_at')
                ->with('user:id,name,profile_photo')
                ->withCount('likedByUsers')
                ->when($user, function($query) use ($user) {
                    return $query->with(['likedByUsers' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }]);
                })
                ->orderByDesc('created_at')
                ->get();

            foreach ($tracks as $track) {
                $feedItems->push([
                    'type' => 'post',
                    'id' => 'track_' . $track->id,
                    'timestamp' => $track->created_at->timestamp,
                    'user' => [
                        'id' => $track->user->id,
                        'name' => $track->user->name,
                        'profile_photo' => $track->user->profile_photo_url,
                        'is_following' => $user ? $user->favoriteDJs()->where('favorited_user_id', $track->user->id)->exists() : false
                    ],
                    'content' => [
                        'type' => 'track',
                        'title' => $track->title,
                        'image' => $track->image_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($track->image_file, '/'))) : null,
                        'audio_url' => $track->audio_file ? url('storage/' . preg_replace('/^storage\//', '', ltrim($track->audio_file, '/'))) : null,
                        'is_premium' => (bool) $track->is_premium,
                    ],
                    'created_at' => $track->created_at->toISOString(),
                    'time_ago' => $this->formatTimeAgo($track->created_at),
                    'likes_count' => $track->liked_by_users_count + 15,
                    'is_liked' => $user ? $track->likedByUsers->contains($user->id) : false,
                ]);
            }


            // Final sorting by timestamp and apply pagination
            $feedItems = $feedItems->sortByDesc('timestamp')->values()->skip($offset)->take($limit);

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
                    'total_posts' => $feedItems->count(),
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
