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

            return response()->json([
                'data' => $paginator->items(),
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

        $sets->getCollection()->transform(function ($set) {
            $set->cover_image = str_starts_with($set->cover_image, '/storage/')
                ? url($set->cover_image)
                : url('/storage/' . $set->cover_image);

            $set->audio_file = str_starts_with($set->audio_file, '/storage/')
                ? url($set->audio_file)
                : url('/storage/' . $set->audio_file);

            return $set;
        });

        return response()->json($sets);
    }
}
