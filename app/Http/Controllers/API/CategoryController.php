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
            $category = Category::with('tracks')->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }
            return response()->json($category->tracks);
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
            $category = Category::with('sets')->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }
            return response()->json($category->sets);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Setler getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a category with its sets and tracks.
     */
    public function show($id)
    {
        try {
            $category = Category::with(['sets', 'tracks.user'])->find($id);
            if (!$category) {
                return response()->json(['message' => 'Kategori bulunamadı.'], 404);
            }
            return response()->json([
                'sets' => $category->sets,
                'tracks' => $category->tracks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kategori getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
