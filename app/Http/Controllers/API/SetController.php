<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;

class SetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/sets",
     *     summary="Get all sets",
     *     tags={"Sets"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of sets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Set")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = 8; // Sayfa başına gösterilecek set sayısı
            $page = (int) $request->get('page', 1);

            $query = Set::query()
                ->when($request->has('user_id'), function ($query) use ($request) {
                    return $query->where('user_id', $request->input('user_id'));
                })
                ->when($request->has('is_premium'), function ($query) use ($request) {
                    return $query->where('is_premium', $request->input('is_premium'));
                })
                ->with(['user'])
                ->latest();

            $paginator = $query->paginate($perPage, array('*'), 'page', $page);
            
            // Token varsa kullanıcıyı al
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $sets = collect($paginator->items())->map(function ($set) use ($user) {
                $setData = $set->toArray();
                
                // Cover image URL'yi tam yap
                $setData['cover_image'] = $set->cover_image ? 
                    (str_starts_with($set->cover_image, '/storage/') ? 
                        url($set->cover_image) : 
                        url('/storage/' . $set->cover_image)) : null;
                
                // Audio file URL'yi tam yap
                $setData['audio_file'] = $set->audio_file ? 
                    (str_starts_with($set->audio_file, '/storage/') ? 
                        url($set->audio_file) : 
                        url('/storage/' . $set->audio_file)) : null;
                
                $setData['isLiked'] = $user ? $user->favoriteSets()->where('set_id', $set->id)->exists() : false;
                return $setData;
            });

            return response()->json([
                'data' => $sets,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Setler getirilirken bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/sets-latest",
     *     summary="Get sets ordered by creation date (latest first)",
     *     tags={"Sets"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of sets ordered by created_at desc",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Set")
     *         )
     *     )
     * )
     */
    public function latest(Request $request)
    {
        $sets = Set::query()
            ->when($request->has('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));
            })
            ->when($request->has('is_premium'), function ($query) use ($request) {
                return $query->where('is_premium', $request->input('is_premium'));
            })
            ->with(['user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Token varsa kullanıcıyı al
        $user = $request->bearerToken() ? auth('sanctum')->user() : null;

        $sets->getCollection()->transform(function ($set) use ($user) {
            // Cover image URL'yi tam yap
            $set->cover_image = str_starts_with($set->cover_image, '/storage/')
                ? url($set->cover_image)
                : url('/storage/' . $set->cover_image);

            // Audio file URL'yi tam yap
            $set->audio_file = str_starts_with($set->audio_file, '/storage/')
                ? url($set->audio_file)
                : url('/storage/' . $set->audio_file);

            // isLiked kontrolü
            $set->isLiked = $user ? $user->favoriteSets()->where('set_id', $set->id)->exists() : false;

            return $set;
        });

        return response()->json($sets);
    }

    /**
     * @OA\Get(
     *     path="/api/sets/search",
     *     summary="Search sets by name",
     *     tags={"Sets"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query for set name",
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
     *         description="Search results for sets",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Set")),
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

            $perPage = 8;
            $page = (int) $request->get('page', 1);

            $setQuery = Set::query()
                ->when($request->has('user_id'), function ($query) use ($request) {
                    return $query->where('user_id', $request->input('user_id'));
                })
                ->when($request->has('is_premium'), function ($query) use ($request) {
                    return $query->where('is_premium', $request->input('is_premium'));
                })
                ->where('name', 'LIKE', '%' . $query . '%')
                ->with(['user'])
                ->orderBy('name', 'asc');

            $paginator = $setQuery->paginate($perPage, array('*'), 'page', $page);
            
            // Token varsa kullanıcıyı al
            $user = $request->bearerToken() ? auth('sanctum')->user() : null;

            $sets = collect($paginator->items())->map(function ($set) use ($user) {
                $setData = $set->toArray();
                
                // Cover image URL'yi tam yap
                $setData['cover_image'] = $set->cover_image ? 
                    (str_starts_with($set->cover_image, '/storage/') ? 
                        url($set->cover_image) : 
                        url('/storage/' . $set->cover_image)) : null;
                
                // Audio file URL'yi tam yap
                $setData['audio_file'] = $set->audio_file ? 
                    (str_starts_with($set->audio_file, '/storage/') ? 
                        url($set->audio_file) : 
                        url('/storage/' . $set->audio_file)) : null;
                
                $setData['isLiked'] = $user ? $user->favoriteSets()->where('set_id', $set->id)->exists() : false;
                return $setData;
            });

            return response()->json([
                'data' => $sets,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Set arama işleminde bir hata oluştu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
