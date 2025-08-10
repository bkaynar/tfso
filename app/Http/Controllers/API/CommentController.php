<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="Yorum yönetimi API endpoints"
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comments",
     *     summary="Yorumları listele",
     *     description="Belirtilen içerik (Set veya Track) için yorumları listeler",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="commentable_type",
     *         in="query",
     *         required=true,
     *         description="Yorum yapılan içerik tipi",
     *         @OA\Schema(
     *             type="string",
     *             enum={"App\Models\Set", "App\Models\Track"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="commentable_id",
     *         in="query",
     *         required=true,
     *         description="Yorum yapılan içeriğin ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Sayfa numarası",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Sayfa başına gösterilecek yorum sayısı",
     *         @OA\Schema(type="integer", default=20, maximum=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="has_more", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="commentable_type",
     *                     type="array",
     *                     @OA\Items(type="string", example="The commentable type field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|in:App\Models\Set,App\Models\Track',
            'commentable_id' => 'required|integer|exists:' . ($request->commentable_type === 'App\Models\Set' ? 'sets' : 'tracks') . ',id',
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:50'
        ]);

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $comments = Comment::with('user:id,name,profile_photo')
            ->where('commentable_type', $request->commentable_type)
            ->where('commentable_id', $request->commentable_id)
            ->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
                'has_more' => $comments->hasMorePages(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Yorum ekle",
     *     description="Belirtilen içerik (Set veya Track) için yeni yorum ekler",
     *     tags={"Comments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content", "commentable_type", "commentable_id"},
     *             @OA\Property(property="content", type="string", maxLength=1000, example="Harika bir set! 🔥"),
     *             @OA\Property(property="commentable_type", type="string", enum={"App\Models\Set", "App\Models\Track"}, example="App\Models\Set"),
     *             @OA\Property(property="commentable_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Yorum başarıyla eklendi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Comment"),
     *             @OA\Property(property="message", type="string", example="Yorum başarıyla eklendi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="İçerik bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="code", type="string", example="NOT_FOUND"),
     *                 @OA\Property(property="message", type="string", example="İçerik bulunamadı")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="content",
     *                     type="array",
     *                     @OA\Items(type="string", example="The content field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'commentable_type' => 'required|in:App\Models\Set,App\Models\Track',
            'commentable_id' => 'required|integer'
        ]);

        // Check if the commentable exists
        $commentableModel = $request->commentable_type;
        if (!$commentableModel::find($request->commentable_id)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'İçerik bulunamadı'
                ]
            ], 404);
        }

        $comment = Comment::create([
            'user_id' => auth('sanctum')->id(),
            'content' => $request->content,
            'commentable_type' => $request->commentable_type,
            'commentable_id' => $request->commentable_id,
            'ip_address' => $request->ip()
        ]);

        $comment->load('user:id,name,profile_photo');

        return response()->json([
            'success' => true,
            'data' => $comment,
            'message' => 'Yorum başarıyla eklendi'
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Yorum sil",
     *     description="Belirtilen yorumu siler. Sadece kendi yorumunu silebilir.",
     *     tags={"Comments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Silinecek yorumun ID'si",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Yorum başarıyla silindi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Yorum başarıyla silindi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Yorum bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="code", type="string", example="NOT_FOUND"),
     *                 @OA\Property(property="message", type="string", example="Yorum bulunamadı")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Yetkisiz erişim",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="code", type="string", example="UNAUTHORIZED"),
     *                 @OA\Property(property="message", type="string", example="Bu yorumu silme yetkiniz yok")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        
        if (!$comment) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Yorum bulunamadı'
                ]
            ], 404);
        }

        // Only allow user to delete their own comment
        if ($comment->user_id !== auth('sanctum')->id()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Bu yorumu silme yetkiniz yok'
                ]
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Yorum başarıyla silindi'
        ]);
    }
}