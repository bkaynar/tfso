<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // accessed_at için Carbon kütüphanesini kullanacağız

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *     name="Access Logs",
 *     description="API Endpoints for managing access logs"
 * )
 */
class AccessLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/access-logs",
     *     summary="Get access logs",
     *     description="Retrieve a list of access logs for authenticated user with optional filters",
     *     tags={"Access Logs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="content_type",
     *         in="query",
     *         description="Filter by content type (e.g., 'track', 'radio')",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of access logs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccessLog")),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=75)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Always filter by authenticated user
        $query = AccessLog::where('user_id', $request->user()->id);

        if ($request->has('content_type')) {
            $query->where('content_type', $request->input('content_type'));
        }

        $accessLogs = $query->latest()->paginate(15); // En son eklenenleri ve sayfalama

        return response()->json($accessLogs);
    }


    /**
     * @OA\Post(
     *     path="/api/access-logs",
     *     summary="Create a new access log entry",
     *     tags={"Access Logs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for the new access log",
     *         @OA\JsonContent(
     *             required={"content_type","content_id"},
     *             @OA\Property(property="content_type", type="string", example="track"),
     *             @OA\Property(property="content_id", type="integer", example=101)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Access log created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AccessLog")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // No need to validate user_id; user is authenticated via bearer token
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|string|max:255',
            'content_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $accessLog = AccessLog::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(), // IP adresini request'ten al
            'content_type' => $request->content_type,
            'content_id' => $request->content_id,
            'accessed_at' => Carbon::now(),
        ]);

        return response()->json($accessLog, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/access-logs/global",
     *     summary="Get all access logs",
     *     description="Retrieve all access logs regardless of user or content type",
     *     tags={"Access Logs"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of all access logs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AccessLog")),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=75)
     *         )
     *     )
     * )
     */
    public function globalIndex(Request $request)
    {
        $accessLogs = AccessLog::latest()->paginate(15);
        
        $accessLogs->getCollection()->transform(function ($log) {
            $data = $log->toArray();
            
            // Content detaylarını ekle
            if ($log->content_type === 'track') {
                $track = \App\Models\Track::find($log->content_id);
                if ($track) {
                    $data['content'] = [
                        'id' => $track->id,
                        'title' => $track->title,
                        'audio_url' => $track->audio_url,
                        'image_url' => $track->image_url,
                        'duration' => $track->duration,
                    ];
                } else {
                    $data['content'] = null;
                }
            } elseif ($log->content_type === 'set') {
                $set = \App\Models\Set::find($log->content_id);
                if ($set) {
                    $data['content'] = [
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
                    ];
                } else {
                    $data['content'] = null;
                }
            } else {
                $data['content'] = null;
            }
            
            return $data;
        });
        
        return response()->json($accessLogs);
    }


    public function show(AccessLog $accessLog)
    {
        return response()->json($accessLog);
    }
}
