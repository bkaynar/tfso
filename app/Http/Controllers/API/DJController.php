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

        // DJ'leri setleri ve parçalarıyla birlikte getir ve en çok içeriğe sahip olanları önce sırala
        $djs = User::role('dj')
            ->with(['sets', 'tracks'])
            ->withCount(['sets', 'tracks'])
            ->get()
            ->sortByDesc(function ($dj) {
                return $dj->sets_count + $dj->tracks_count;
            })
            ->values();

        // Yanıtı formatla
        $result = $djs->map(function ($dj) use ($user) {
            return [
                'id' => $dj->id,
                'name' => $dj->name,
                'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                'bio' => $dj->bio,
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
                        'audio_file' => $set->audio_file ? url($set->audio_file) : null,
                    ];
                }),
                'tracks' => $dj->tracks->map(function ($track) {
                    return [
                        'id' => $track->id,
                        'title' => $track->title,
                        'audio_url' => $track->audio_url,
                        'image_url' => $track->image_url,
                    ];
                }),
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
                ->with(['sets', 'tracks'])
                ->orderBy('name', 'asc');

            $paginator = $query->paginate($perPage, array('*'), 'page', $page);

            $djs = collect($paginator->items())->map(function ($dj) use ($user) {
                return [
                    'id' => $dj->id,
                    'name' => $dj->name,
                    'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                    'bio' => $dj->bio,
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
                            'audio_file' => $set->audio_file ? url($set->audio_file) : null,
                        ];
                    }),
                    'tracks' => $dj->tracks->map(function ($track) {
                        return [
                            'id' => $track->id,
                            'title' => $track->title,
                            'audio_url' => $track->audio_url,
                            'image_url' => $track->image_url,
                        ];
                    }),
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
                ->with(['sets', 'tracks'])
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderBy('name', 'asc');

            $paginator = $djQuery->paginate($perPage, array('*'), 'page', $page);

            $djs = collect($paginator->items())->map(function ($dj) use ($user) {
                return [
                    'id' => $dj->id,
                    'name' => $dj->name,
                    'profile_photo' => $dj->profile_photo ? url('/storage/' . $dj->profile_photo) : null,
                    'bio' => $dj->bio,
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
                            'audio_file' => $set->audio_file ? url($set->audio_file) : null,
                        ];
                    }),
                    'tracks' => $dj->tracks->map(function ($track) {
                        return [
                            'id' => $track->id,
                            'title' => $track->title,
                            'audio_url' => $track->audio_url,
                            'image_url' => $track->image_url,
                        ];
                    }),
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
     * @OA\Post(
     *     path="/api/djs",
     *     summary="Create a new DJ",
     *     tags={"DJs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for the new DJ",
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Yeni DJ"),
     *             @OA\Property(property="email", type="string", format="email", example="yeni.dj@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="profile_photo", type="string", example="path/to/photo.jpg"),
     *             @OA\Property(property="bio", type="string", example="Yeni DJ'in biyografisi."),
     *             @OA\Property(property="instagram", type="string", example="yenidj"),
     *             @OA\Property(property="twitter", type="string", example="yenidj"),
     *             @OA\Property(property="facebook", type="string", example="https://facebook.com/yenidj"),
     *             @OA\Property(property="tiktok", type="string", example="yenidj")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="DJ created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'profile_photo' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dj = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Şifreyi hash'lemeyi unutmayın!
            'profile_photo' => $request->profile_photo,
            'bio' => $request->bio,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'facebook' => $request->facebook,
            'tiktok' => $request->tiktok,
        ]);

        $dj->assignRole('dj');

        return response()->json($dj, 201);
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
        $dj = User::role('dj')->find($id);

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
                ];
            }),
            'tracks' => $dj->tracks->map(function ($track) {
                return [
                    'id' => $track->id,
                    'title' => $track->title,
                    'audio_url' => $track->audio_url,
                    'image_url' => $track->image_url,
                ];
            }),
            'isLiked' => $user ? $user->favoriteDJs()->where('favorited_user_id', $dj->id)->exists() : false,
        ];

        return response()->json($response);
    }

    /**
     * @OA\Post(
     *     path="/api/djs/{id}",
     *     summary="Update a DJ's information",
     *     tags={"DJs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the DJ to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data to update for the DJ. Use multipart/form-data for file uploads.",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Güncel DJ Adı"),
     *                 @OA\Property(property="bio", type="string", example="Güncel biyografi."),
     *                 @OA\Property(property="profile_photo", type="string", format="binary", description="DJ's new profile photo"),
     *                 @OA\Property(property="instagram", type="string", example="gunceldj"),
     *                 @OA\Property(property="twitter", type="string", example="gunceldj"),
     *                 @OA\Property(property="facebook", type="string", example="https://facebook.com/gunceldj"),
     *                 @OA\Property(property="tiktok", type="string", example="gunceldj"),
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Must be PUT for this endpoint.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="DJ updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="DJ not found"
     *     ),
     *      @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        // Güncellenecek DJ'i bul
        $dj = User::role('dj')->find($id);

        if (!$dj) {
            return response()->json(['message' => 'DJ not found'], 404);
        }

        // Sadece admin veya DJ'in kendisi profili güncelleyebilir
        $current = Auth::user();
        // Sadece admin veya DJ kendisi güncelleyebilir
        if (!$current->roles->contains('name', 'admin') && $current->id !== $dj->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Verileri güncelle
        $dj->fill($request->only(['name', 'bio', 'instagram', 'twitter', 'facebook', 'tiktok']));

        // Profil fotoğrafını güncelle
        if ($request->hasFile('profile_photo')) {
            // Eski fotoğrafı sil (isteğe bağlı)
            if ($dj->profile_photo && Storage::exists($dj->profile_photo)) {
                Storage::delete($dj->profile_photo);
            }
            $path = $request->file('profile_photo')->store('djs/profile_photos', 'public');
            $dj->profile_photo = $path;
        }

        $dj->save();

        return response()->json($dj, 200);
    }


    /**
     * @OA\Delete(
     *     path="/api/djs/{id}",
     *     summary="Delete a DJ",
     *     tags={"DJs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the DJ to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="DJ deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="DJ not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        // Yalnızca belirli rollere sahip kullanıcıların DJ silmesine izin ver
        $current = Auth::user();
        // Sadece admin silebilir
        if (!$current->roles->contains('name', 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $dj = User::role('dj')->find($id);

        if (!$dj) {
            return response()->json(['message' => 'DJ not found'], 404);
        }

        $dj->delete();

        return response()->json(null, 204); // No Content
    }
}
