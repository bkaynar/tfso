<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// User modelini kullanacağımız için import ediyoruz
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Rule::unique kullanmak için

/**
 * @OA\Components(
 *     @OA\Schema(
 *         schema="User",
 *         title="User",
 *         description="Model representing a user in the system",
 *         @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the user", readOnly=true),
 *         @OA\Property(property="name", type="string", description="Name of the user"),
 *         @OA\Property(property="email", type="string", format="email", description="Email address of the user"),
 *         @OA\Property(property="password", type="string", format="password", description="Password for the user account"),
 *         @OA\Property(property="profile_photo", type="string", description="URL of the user's profile photo"),
 *         @OA\Property(property="bio", type="string", description="Short biography of the user"),
 *         @OA\Property(property="instagram", type="string", description="Instagram handle of the user"),
 *         @OA\Property(property="twitter", type="string", description="Twitter handle of the user"),
 *         @OA\Property(property="facebook", type="string", description="Facebook profile URL of the user"),
 *         @OA\Property(property="tiktok", type="string", description="TikTok handle of the user"),
 *     )
 * )
 */
class DJController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/djs",
     *     summary="Get all DJs",
     *     tags={"DJs"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of DJs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        // DJ'leri setleri, parçaları ve etkinlikleriyle birlikte getir ve en çok içeriğe sahip olanları önce sırala
        $djs = User::role('dj')
            ->with(['sets', 'tracks' => function($query) {
                $query->withCount('likedByUsers');
            }, 'events'])
            ->withCount(['sets', 'tracks', 'events'])
            ->get()
            ->sortByDesc(function ($dj) {
                return $dj->sets_count + $dj->tracks_count + $dj->events_count;
            })
            ->values();

        // Yanıtı formatla
        $result = $djs->map(function ($dj) use ($user) {
            return [
                'id' => $dj->id,
                'name' => $dj->name,
                'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                'isLiked' => $user ? $user->favoriteDJs()->where('favorited_user_id', $dj->id)->exists() : false,
            ];
        });

        return response()->json($result, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/djs-paginated",
     *     summary="Get paginated list of DJs ordered A-Z",
     *     tags={"DJs"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of DJs ordered alphabetically",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function paginatedIndex(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $perPage = 6;
            $page = (int) $request->get('page', 1);

            $query = User::role('dj')
                ->with(['sets', 'tracks' => function($query) {
                    $query->withCount('likedByUsers');
                }, 'events'])
                ->orderBy('name', 'asc');

            $paginator = $query->paginate($perPage, array('*'), 'page', $page);

            $djs = collect($paginator->items())->map(function ($dj) use ($user) {
                return [
                    'id' => $dj->id,
                    'name' => $dj->name,
                    'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                    'isLiked' => $user ? $user->favoriteDJs()->where('favorited_user_id', $dj->id)->exists() : false,
                ];
            });

            return response()->json([
                'data' => $djs,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'DJ\'ler getirilirken bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/djs/search",
     *     summary="Search DJs by name",
     *     tags={"DJs"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query for DJ name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results for DJs",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - query parameter required"
     *     )
     * )
     */
    public function search(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $query = $request->get('query');

            if (empty($query)) {
                return response()->json([
                    'message' => 'Arama sorgusu gereklidir.',
                ], 422);
            }

            $perPage = 6;
            $page = (int) $request->get('page', 1);

            $djQuery = User::role('dj')
                ->with(['sets', 'tracks' => function($query) {
                    $query->withCount('likedByUsers');
                }, 'events'])
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderBy('name', 'asc');

            $paginator = $djQuery->paginate($perPage, array('*'), 'page', $page);

            $djs = collect($paginator->items())->map(function ($dj) use ($user) {
                return [
                    'id' => $dj->id,
                    'name' => $dj->name,
                    'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                    'isLiked' => $user ? $user->favoriteDJs()->where('favorited_user_id', $dj->id)->exists() : false,
                ];
            });

            return response()->json([
                'data' => $djs,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'DJ arama işleminde bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    

    /**
     * @OA\Get(
     *     path="/api/djs/{id}",
     *     summary="Get a specific DJ's details with recent sets and tracks",
     *     tags={"DJs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the DJ",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the specified DJ with recent sets and tracks",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="DJ Örnek"),
     *             @OA\Property(property="bio", type="string", example="Enerjik setlerle dans pistini coşturuyorum!"),
     *             @OA\Property(property="profile_photo", type="string", example="https://example.com/storage/users/photo.jpg"),
     *             @OA\Property(
     *                 property="social_media",
     *                 type="object",
     *                 @OA\Property(property="instagram", type="string", example="https://instagram.com/djornek"),
     *                 @OA\Property(property="twitter", type="string", example="https://twitter.com/dj_ornek"),
     *                 @OA\Property(property="facebook", type="string", example="https://facebook.com/djornek"),
     *                 @OA\Property(property="tiktok", type="string", example="https://tiktok.com/@djornek")
     *             ),
     *             @OA\Property(
     *                 property="sets",
     *                 type="array",
     *                 description="Last 3 sets of the DJ",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Summer Vibes Mix"),
     *                     @OA\Property(property="cover_image", type="string", example="https://example.com/storage/sets/cover.jpg"),
     *                     @OA\Property(property="audio_file", type="string", example="https://example.com/storage/sets/audio.mp3")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="tracks",
     *                 type="array",
     *                 description="Last 3 tracks of the DJ",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Epic Beat"),
     *                     @OA\Property(property="audio_file", type="string", example="https://example.com/storage/tracks/audio.mp3"),
     *                     @OA\Property(property="duration", type="integer", example=180)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="DJ not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="DJ not found")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        // DJ rolüne sahip kullanıcıyı bul
        $dj = User::role('dj')
            ->with(['tracks' => function($query) {
                $query->withCount('likedByUsers');
            }])
            ->find($id);

        if (!$dj) {
            return response()->json(['message' => 'DJ not found'], 404);
        }

        // Yanıtı formatla
        $response = [
            'id' => $dj->id,
            'name' => $dj->name,
            'bio' => $dj->bio,
            'profile_photo' => $dj->profile_photo ? url($dj->profile_photo) : null,
            'social_media' => [
                'instagram' => $dj->instagram ? "https://instagram.com/{$dj->instagram}" : null,
                'twitter' => $dj->twitter ? "https://twitter.com/{$dj->twitter}" : null,
                'facebook' => $dj->facebook ? $dj->facebook : null,
                'tiktok' => $dj->tiktok ? "https://tiktok.com/@{$dj->tiktok}" : null
            ],
            'sets' => $dj->sets->map(function ($set) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'cover_image' => $set->cover_image ? url($set->cover_image) : null,
                    'audio_file' => $set->audio_file ? url('/storage/' . $set->audio_file) : null,
                    'is_premium' => $set->is_premium,
                ];
            }),
            'tracks' => $dj->tracks->map(function ($track) {
                return [
                    'id' => $track->id,
                    'title' => $track->title,
                    'audio_url' => $track->audio_url,
                    'image_url' => $track->image_url,
                    'is_premium' => $track->is_premium,
                    'likes_count' => $track->liked_by_users_count+15, // likedByUsers count + 15 for demo purposes
                ];
            }),
            'events' => $dj->events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'event_date' => $event->event_date,
                    'time' => $event->event_time,
                    'location' => $event->location,
                    'image' => $event->photo ? url('/storage/' . $event->photo) : null,
                ];
            }),
            'isLiked' => $user ? $user->favoriteDJs()->where('favorited_user_id', $dj->id)->exists() : false,
        ];

        return response()->json($response);
    }
}
