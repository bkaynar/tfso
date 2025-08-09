<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Place Manager",
 *     description="API Endpoints for managing places"
 * )
 */
class PlaceManagerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/places",
     *     operationId="getPlaces",
     *     tags={"Place Manager"},
     *     summary="Get all places",
     *     description="Returns a list of all places with their images and user information",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Sample Place"),
     *                     @OA\Property(property="description", type="string", example="A beautiful place"),
     *                     @OA\Property(property="google_maps_url", type="string", example="https://maps.google.com/..."),
     *                     @OA\Property(property="apple_maps_url", type="string", example="https://maps.apple.com/..."),
     *                     @OA\Property(property="location", type="string", example="Istanbul, Turkey"),
     *                     @OA\Property(property="is_premium", type="boolean", example=false),
     *                     @OA\Property(property="premium_expires_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="premium_trial_used", type="boolean", example=false),
     *                     @OA\Property(property="phone", type="string", example="+90 555 123 4567"),
     *                     @OA\Property(property="facebook_url", type="string", nullable=true),
     *                     @OA\Property(property="twitter_url", type="string", nullable=true),
     *                     @OA\Property(property="instagram_url", type="string", nullable=true),
     *                     @OA\Property(property="youtube_url", type="string", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="images", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="user", type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $places = Place::with(['images','events'])
            ->orderBy('id', 'asc')
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $places
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}",
     *     operationId="getPlace",
     *     tags={"Place Manager"},
     *     summary="Get place by ID",
     *     description="Returns a specific place with its images and user information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Place ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sample Place"),
     *                 @OA\Property(property="description", type="string", example="A beautiful place"),
     *                 @OA\Property(property="google_maps_url", type="string", example="https://maps.google.com/..."),
     *                 @OA\Property(property="apple_maps_url", type="string", example="https://maps.apple.com/..."),
     *                 @OA\Property(property="location", type="string", example="Istanbul, Turkey"),
     *                 @OA\Property(property="is_premium", type="boolean", example=false),
     *                 @OA\Property(property="premium_expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="premium_trial_used", type="boolean", example=false),
     *                 @OA\Property(property="phone", type="string", example="+90 555 123 4567"),
     *                 @OA\Property(property="facebook_url", type="string", nullable=true),
     *                 @OA\Property(property="twitter_url", type="string", nullable=true),
     *                 @OA\Property(property="instagram_url", type="string", nullable=true),
     *                 @OA\Property(property="youtube_url", type="string", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="images", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="events", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Place not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Place not found")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $places = Place::with(['images', 'events'])
            ->orderBy('id', 'asc')
            ->get();

        if ($places->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Places not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $places
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/places/last",
     *     operationId="getLastPlace",
     *     tags={"Place Manager"},
     *     summary="Get the latest places",
     *     description="Returns the 5 most recently created places with their images and events",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sample Place"),
     *                 @OA\Property(property="description", type="string", example="A beautiful place"),
     *                 @OA\Property(property="google_maps_url", type="string", example="https://maps.google.com/..."),
     *                 @OA\Property(property="apple_maps_url", type="string", example="https://maps.apple.com/..."),
     *                 @OA\Property(property="location", type="string", example="Istanbul, Turkey"),
     *                 @OA\Property(property="is_premium", type="boolean", example=false),
     *                 @OA\Property(property="premium_expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="premium_trial_used", type="boolean", example=false),
     *                 @OA\Property(property="phone", type="string", example="+90 555 123 4567"),
     *                 @OA\Property(property="facebook_url", type="string", nullable=true),
     *                 @OA\Property(property="twitter_url", type="string", nullable=true),
     *                 @OA\Property(property="instagram_url", type="string", nullable=true),
     *                 @OA\Property(property="youtube_url", type="string", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="images", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="events", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No places found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No places found")
     *         )
     *     )
     * )
     */
    public function lastPlace(): JsonResponse
    {
        $lastPlaces = Place::with(['images', 'events'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($lastPlaces->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No places found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lastPlaces
        ]);
    }
}
