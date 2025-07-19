<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tracks",
     *     summary="Tüm şarkıları listeler",
     *     tags={"Tracks"},
     *     @OA\Response(
     *         response=200,
     *         description="Şarkıların listesi",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Track")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Sunucu hatası"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = 4;
            $page = (int) $request->get('page', 1);
            $paginator = Track::with(['category', 'user'])
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            // user null olabilir, sorun değil
            $user = auth('sanctum')->user();

            $tracks = collect($paginator->items())->map(function ($track) use ($user) {
                $trackData = $track->toArray();
                $trackData['isLiked'] = $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false;
                return $trackData;
            });

            return response()->json([
                'data' => $tracks,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Şarkılar getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * @OA\Post(
     *     path="/api/tracks",
     *     summary="Yeni bir şarkı oluşturur",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Şarkı bilgileri",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "audio_file", "category_id"},
     *                 @OA\Property(property="title", type="string", example="Harika Şarkı"),
     *                 @OA\Property(property="description", type="string", example="Bu şarkı çok güzel."),
     *                 @OA\Property(property="audio_file", type="string", format="binary", description="Ses dosyası (mp3, wav vb.)"),
     *                 @OA\Property(property="image_file", type="string", format="binary", description="Kapak resmi (jpg, png vb.)"),
     *                 @OA\Property(property="is_premium", type="boolean", example=false),
     *                 @OA\Property(property="iap_product_id", type="string", example="premium_track_01"),
     *                 @OA\Property(property="category_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Şarkı başarıyla oluşturuldu",
     *         @OA\JsonContent(ref="#/components/schemas/Track")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Doğrulama hatası"
     *     ),
     *      @OA\Response(
     *         response=401,
     *         description="Yetkisiz"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'required|file|mimes:mp3,wav,aac,flac|max:20480', // max 20MB
            'image_file' => 'nullable|file|image|max:5120', // max 5MB
            'is_premium' => 'sometimes|boolean',
            'iap_product_id' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['title', 'description', 'is_premium', 'iap_product_id', 'category_id']);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('audio_file')) {
            $data['audio_file'] = $request->file('audio_file')->store('tracks/audio', 'public');
        }

        if ($request->hasFile('image_file')) {
            $data['image_file'] = $request->file('image_file')->store('tracks/images', 'public');
        }

        $track = Track::create($data);

        return response()->json($track->load('category', 'user'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tracks/{id}",
     *     summary="Belirli bir şarkının detaylarını getirir",
     *     tags={"Tracks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Şarkı ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Şarkı detayları",
     *         @OA\JsonContent(ref="#/components/schemas/Track")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Şarkı bulunamadı"
     *     )
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $track = Track::with(['category', 'user'])->findOrFail($id);

            // Kullanıcı varsa getir, yoksa null
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $trackData = $track->toArray();
            $trackData['isLiked'] = $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false;

            return response()->json($trackData);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Şarkı bulunamadı.'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Şarkı getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/tracks/{id}",
     *     summary="Bir şarkıyı günceller",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Güncellenecek şarkının ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Güncellenecek şarkı bilgileri",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="audio_file", type="string", format="binary"),
     *                 @OA\Property(property="image_file", type="string", format="binary"),
     *                 @OA\Property(property="is_premium", type="boolean"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Form-data ile PUT isteği göndermek için gereklidir.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Şarkı başarıyla güncellendi",
     *         @OA\JsonContent(ref="#/components/schemas/Track")
     *     ),
     *     @OA\Response(response=404, description="Şarkı bulunamadı"),
     *     @OA\Response(response=422, description="Doğrulama hatası"),
     *     @OA\Response(response=403, description="Yetkisiz işlem")
     * )
     */
    public function update(Request $request, $id)
    {
        $track = Track::find($id);

        if (!$track) {
            return response()->json(['message' => 'Şarkı bulunamadı.'], 404);
        }

        // Politika veya Gate ile yetki kontrolü yapılabilir.
        // $this->authorize('update', $track);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'sometimes|file|mimes:mp3,wav,aac,flac|max:20480',
            'image_file' => 'sometimes|file|image|max:5120',
            'is_premium' => 'sometimes|boolean',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(['title', 'description', 'is_premium', 'category_id']);

        if ($request->hasFile('audio_file')) {
            Storage::disk('public')->delete($track->audio_file);
            $data['audio_file'] = $request->file('audio_file')->store('tracks/audio', 'public');
        }

        if ($request->hasFile('image_file')) {
            Storage::disk('public')->delete($track->image_file);
            $data['image_file'] = $request->file('image_file')->store('tracks/images', 'public');
        }

        $track->update($data);

        return response()->json($track->load('category', 'user'));
    }

    /**
     * @OA\Delete(
     *     path="/api/tracks/{id}",
     *     summary="Bir şarkıyı siler",
     *     tags={"Tracks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Silinecek şarkının ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Şarkı başarıyla silindi"
     *     ),
     *     @OA\Response(response=404, description="Şarkı bulunamadı"),
     *     @OA\Response(response=403, description="Yetkisiz işlem")
     * )
     */
    public function destroy($id)
    {
        $track = Track::find($id);

        if (!$track) {
            return response()->json(['message' => 'Şarkı bulunamadı.'], 404);
        }

        // Politika veya Gate ile yetki kontrolü yapılabilir.
        // $this->authorize('delete', $track);

        // İlişkili dosyaları sil
        Storage::disk('public')->delete($track->audio_file);
        if ($track->image_file) {
            Storage::disk('public')->delete($track->image_file);
        }

        $track->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/tracks/new-releases",
     *     summary="🔥 Yeni çıkan parçaları getirir (Son 30 gün)",
     *     description="En son eklenen parçaları created_at tarihine göre sıralar. Son 30 günde eklenen parçaları önceliklendirir.",
     *     tags={"Tracks"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Kaç parça getirileceği (varsayılan: 20, maksimum: 50)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=50, default=20)
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Kaç gün öncesine kadar bakılacağı (varsayılan: 30)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=365, default=30)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="🎵 Yeni çıkan parçalar başarıyla getirildi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="🔥 Yeni çıkan parçalar getirildi!"),
     *             @OA\Property(property="total_count", type="integer", example=25),
     *             @OA\Property(property="period", type="string", example="Son 30 gün"),
     *             @OA\Property(
     *                 property="tracks",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="title", type="string", example="Epic Beat 2024"),
     *                     @OA\Property(property="artist_name", type="string", example="DJ Awesome"),
     *                     @OA\Property(property="category_name", type="string", example="Electronic"),
     *                     @OA\Property(property="audio_file", type="string", example="https://api.example.com/storage/tracks/epic-beat.mp3"),
     *                     @OA\Property(property="image_file", type="string", example="https://api.example.com/storage/tracks/epic-beat-cover.jpg"),
     *                     @OA\Property(property="release_date", type="string", format="date-time", example="2024-06-28T15:30:00Z"),
     *                     @OA\Property(property="days_since_release", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Sunucu hatası"
     *     )
     * )
     */
    public function newReleases(Request $request)
    {
        try {
            // Parametreleri al ve varsayılan değerleri belirle
            $limit = min((int)$request->get('limit', 20), 50); // Maksimum 50
            $days = min((int)$request->get('days', 30), 365);   // Maksimum 365 gün

            // Son X gün içinde eklenen parçaları getir
            $cutoffDate = now()->subDays($days);

            // Auth olan kullanıcıyı al (opsiyonel)
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $tracks = Track::with(['category', 'user'])
                ->where('created_at', '>=', $cutoffDate)
                ->latest('created_at') // En yeni olanlar önce
                ->limit($limit)
                ->get()
                ->map(function ($track) use ($user) {
                    return [
                        'id' => $track->id,
                        'title' => $track->title,
                        'artist_name' => $track->user ? $track->user->name : 'Bilinmeyen Sanatçı',
                        'category_name' => $track->category ? $track->category->name : 'Kategori Yok',
                        'audio_file' => $track->audio_file ? url('/storage/' . $track->audio_file) : null,
                        'image_file' => $track->image_file ? url('/storage/' . $track->image_file) : null,
                        'release_date' => $track->created_at->toISOString(),
                        'days_since_release' => $track->created_at->diffInDays(now()),
                        'is_premium' => $track->is_premium,
                        'isLiked' => $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false,
                    ];
                });

            return response()->json([
                'message' => '🔥 Yeni çıkan parçalar getirildi!',
                'total_count' => $tracks->count(),
                'period' => "Son {$days} gün",
                'tracks' => $tracks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '😞 Yeni parçalar getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tracks/search",
     *     summary="Search tracks by title",
     *     tags={"Tracks"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query for track title",
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
     *         description="Search results for tracks",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Track")),
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

            $trackQuery = Track::query()
                ->with(['user', 'category'])
                ->where('title', 'LIKE', '%' . $query . '%')
                ->orderBy('title', 'asc');

            $paginator = $trackQuery->paginate($perPage, array('*'), 'page', $page);
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $tracks = collect($paginator->items())->map(function ($track) use ($user) {
                $trackData = $track->toArray();
                $trackData['isLiked'] = $user ? $user->favoriteTracks()->where('track_id', $track->id)->exists() : false;
                return $trackData;
            });

            return response()->json([
                'data' => $tracks,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Track arama işleminde bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
