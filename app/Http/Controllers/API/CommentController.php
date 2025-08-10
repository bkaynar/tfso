<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
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