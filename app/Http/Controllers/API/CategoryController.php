<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     properties={
 *         @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *         @OA\Property(property="name", type="string", example="Pop"),
 *         @OA\Property(property="image", type="string", nullable=true, example="categories/pop.jpg"),
 *         @OA\Property(property="image_url", type="string", readOnly="true", nullable=true, example="http://localhost/storage/categories/pop.jpg"),
 *         @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true")
 *     }
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Tüm kategorileri listeler",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Kategorilerin listesi",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Sunucu hatası",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Kategoriler getirilirken bir hata oluştu."),
     *              @OA\Property(property="error", type="string")
     *          )
     *     )
     * )
     */
    public function index()
    {
        try {
            $categories = Category::withCount(['tracks', 'sets'])
                ->get()
                ->sortByDesc(function ($category) {
                    return $category->tracks_count + $category->sets_count;
                })
                ->values();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kategoriler getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories-paginated",
     *     summary="Get paginated list of categories ordered by content count",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of categories ordered by content count",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
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
            $perPage = 10;
            $page = (int) $request->get('page', 1);

            $query = Category::withCount(['tracks', 'sets'])
                ->orderByRaw('(tracks_count + sets_count) DESC');

            $paginator = $query->paginate($perPage, array('*'), 'page', $page);

            return response()->json([
                'data' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kategoriler getirilirken bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/search",
     *     summary="Search categories by name",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query for category name",
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
     *         description="Search results for categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
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
            $query = $request->get('query');
            
            if (empty($query)) {
                return response()->json([
                    'message' => 'Arama sorgusu gereklidir.',
                ], 422);
            }

            $perPage = 10;
            $page = (int) $request->get('page', 1);

            $categoryQuery = Category::withCount(['tracks', 'sets'])
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderBy('name', 'asc');

            $paginator = $categoryQuery->paginate($perPage, array('*'), 'page', $page);

            return response()->json([
                'data' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kategori arama işleminde bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/categories/bulk",
     * summary="Toplu kategori ekleme işlemi",
     * tags={"Categories"},
     * @OA\RequestBody(
     * required=true,
     * description="Eklenmek istenen kategorilerin listesi",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="categories",
     * type="array",
     * description="Kategori objelerinin listesi",
     * @OA\Items(
     * type="object",
     * @OA\Property(
     * property="name",
     * type="string",
     * description="Kategori adı",
     * example="Pop Müzik"
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Kategoriler başarıyla eklendi",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Kategoriler başarıyla eklendi."),
     * @OA\Property(property="count", type="integer", example=3)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Doğrulama hatası",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Doğrulama hatası"),
     * @OA\Property(
     * property="errors",
     * type="object",
     * example={"categories.0.name": {"The categories.0.name field is required."}}
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Sunucu hatası",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Kategoriler eklenirken bir hata oluştu."),
     * @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
     * )
     * )
     * )
     */
    public function storeBulk(Request $request)
    {
        // Gelen verinin bir dizi olduğundan emin olmak için doğrulama
        // Her bir kategori nesnesinin sadece 'name' alanını içerdiğini kontrol ediyoruz.
        try {
            $validator = Validator::make($request->all(), [
                'categories' => 'required|array', // 'categories' anahtarının zorunlu ve dizi olmasını sağlar
                'categories.*.name' => 'required|string|max:255', // Dizideki her bir elemanın 'name' alanı
                // 'image' alanı artık gerekli değil, bu yüzden doğrulama kuralından çıkarıldı.
            ]);

            $validator->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }

        $categories = $request->input('categories');

        // Toplu ekleme işlemi için Category::insert() kullanıyoruz.
        // Bu, her bir model için ayrı ayrı kaydetme çağrısı yapmaktan çok daha verimlidir.
        try {
            Category::insert($categories); // Veritabanına toplu ekleme
            return response()->json([
                'message' => 'Kategoriler başarıyla eklendi.',
                'count' => count($categories)
            ], 201); // HTTP 201 Created
        } catch (\Exception $e) {
            // Veritabanı ekleme sırasında oluşabilecek hataları yakala
            return response()->json([
                'message' => 'Kategoriler eklenirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 Internal Server Error
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/tracks",
     *     summary="Bir kategoriye ait tüm şarkıları listeler",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Kategori ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Şarkıların listesi",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Track")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategori bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Kategori bulunamadı.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Sunucu hatası"
     *     )
     * )
     */
    public function getTracksByCategory($id)
    {
        try {
            $category = Category::with('tracks.user')->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }
            
            // Token varsa kullanıcıyı al
            $user = request()->bearerToken() ? auth('sanctum')->user() : null;
            
            $tracks = $category->tracks->map(function ($track) use ($user) {
                return [
                    'id' => $track->id,
                    'title' => $track->title,
                    'audio_url' => $track->audio_url,
                    'image_url' => $track->image_url,
                    'duration' => $track->duration,
                    'is_premium' => $track->is_premium,
                    'created_at' => $track->created_at,
                    'updated_at' => $track->updated_at,
                    'isLiked' => $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false,
                    'user' => $track->user ? [
                        'id' => $track->user->id,
                        'name' => $track->user->name,
                        'profile_photo' => $track->user->profile_photo ? url('/storage/' . $track->user->profile_photo) : null,
                    ] : null
                ];
            });
            
            return response()->json($tracks);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Şarkılar getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/sets",
     *     summary="Bir kategoriye ait tüm setleri listeler",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Kategori ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setlerin listesi",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Set")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Kategori bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Kategori bulunamadı.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Sunucu hatası"
     *     )
     * )
     */
    public function getSetsByCategory($id)
    {
        try {
            $category = Category::with('sets.user')->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }
            
            // Token varsa kullanıcıyı al
            $user = request()->bearerToken() ? auth('sanctum')->user() : null;
            
            $sets = $category->sets->map(function ($set) use ($user) {
                return [
                    'id' => $set->id,
                    'name' => $set->name,
                    'cover_image' => $set->cover_image ? 
                        (str_starts_with($set->cover_image, '/storage/') ? 
                            url($set->cover_image) : 
                            url('/storage/' . $set->cover_image)) : null,
                    'audio_file' => $set->audio_file ? 
                        (str_starts_with($set->audio_file, '/storage/') ? 
                            url($set->audio_file) : 
                            url('/storage/' . $set->audio_file)) : null,
                    'is_premium' => $set->is_premium,
                    'created_at' => $set->created_at,
                    'updated_at' => $set->updated_at,
                    'isLiked' => $user ? $user->favoriteSets()->where('set_id', $set->id)->exists() : false,
                    'user' => $set->user ? [
                        'id' => $set->user->id,
                        'name' => $set->user->name,
                        'profile_photo' => $set->user->profile_photo ? url('/storage/' . $set->user->profile_photo) : null,
                    ] : null
                ];
            });
            
            return response()->json($sets);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Setler getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get category with sets and/or tracks",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Content type filter",
     *         @OA\Schema(type="string", enum={"sets", "tracks", "all"}, default="all")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category with filtered content",
     *         @OA\JsonContent(
     *             @OA\Property(property="category", ref="#/components/schemas/Category"),
     *             @OA\Property(
     *                 property="content",
     *                 type="array",
     *                 description="Array of sets and/or tracks with type identifier",
     *                 @OA\Items(
     *                     @OA\Property(property="type", type="string", enum={"set", "track"}),
     *                     @OA\Property(property="data", type="object", description="Set or Track object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $category = Category::with(['sets.user', 'tracks.user'])->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }

            // Token varsa kullanıcıyı al
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $type = $request->get('type', 'all'); // Default: all
            $mixedContent = collect();

            // Sets'leri ekle (eğer type 'sets' veya 'all' ise)
            if ($type === 'sets' || $type === 'all') {
                foreach ($category->sets as $set) {
                    $mixedContent->push([
                        'type' => 'set',
                        'data' => [
                            'id' => $set->id,
                            'name' => $set->name,
                            'cover_image' => $set->cover_image ? 
                                (str_starts_with($set->cover_image, '/storage/') ? 
                                    url($set->cover_image) : 
                                    url('/storage/' . $set->cover_image)) : null,
                            'audio_file' => $set->audio_file ? 
                                (str_starts_with($set->audio_file, '/storage/') ? 
                                    url($set->audio_file) : 
                                    url('/storage/' . $set->audio_file)) : null,
                            'is_premium' => $set->is_premium,
                            'created_at' => $set->created_at,
                            'isLiked' => $user ? $user->favoriteSets()->where('set_id', $set->id)->exists() : false,
                            'user' => $set->user ? [
                                'id' => $set->user->id,
                                'name' => $set->user->name,
                                'profile_photo' => $set->user->profile_photo ? url('/storage/' . $set->user->profile_photo) : null,
                            ] : null
                        ]
                    ]);
                }
            }

            // Tracks'leri ekle (eğer type 'tracks' veya 'all' ise)
            if ($type === 'tracks' || $type === 'all') {
                foreach ($category->tracks as $track) {
                    $mixedContent->push([
                        'type' => 'track',
                        'data' => [
                            'id' => $track->id,
                            'title' => $track->title,
                            'audio_url' => $track->audio_url,
                            'image_url' => $track->image_url,
                            'duration' => $track->duration,
                            'is_premium' => $track->is_premium,
                            'created_at' => $track->created_at,
                            'isLiked' => $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false,
                            'user' => $track->user ? [
                                'id' => $track->user->id,
                                'name' => $track->user->name,
                                'profile_photo' => $track->user->profile_photo ? url('/storage/' . $track->user->profile_photo) : null,
                            ] : null
                        ]
                    ]);
                }
            }

            // Sıralama: type 'all' ise önce setler sonra trackler
            if ($type === 'all') {
                $mixedContent = $mixedContent->sortBy(function ($item) {
                    return $item['type'] === 'set' ? 0 : 1;
                })->values();
            }

            return response()->json([
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                    'image_url' => $category->image ? url('/storage/' . $category->image) : null,
                ],
                'content' => $mixedContent,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kategori getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
