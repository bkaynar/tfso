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
    public function index()
    {
        try {
            // Eager loading ile ilişkili modelleri de getiriyoruz.
            $tracks = Track::with(['category', 'user'])->latest()->paginate(15);
            return response()->json($tracks);
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
        $data['user_id'] = auth()->id();

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
    public function show($id)
    {
        try {
            $track = Track::with(['category', 'user'])->findOrFail($id);
            return response()->json($track);
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
}
