<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Radio;
use App\Models\Set;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Giriş yapmış kullanıcının tüm favorilerini listeler",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Favorilerin listesi",
     *         @OA\JsonContent(
     *             @OA\Property(property="tracks", type="array", @OA\Items(ref="#/components/schemas/Track")),
     *             @OA\Property(property="sets", type="array", @OA\Items(ref="#/components/schemas/Set")),
     *             @OA\Property(property="radios", type="array", @OA\Items(ref="#/components/schemas/Radio")),
     *             @OA\Property(property="djs", type="array", @OA\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Yetkisiz")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tracks = $user->favoriteTracks()->with('user')->get()->map(function ($track) {
            $data = $track->toArray();
            $data['artist_name'] = $track->user ? $track->user->name : null;
            return $data;
        });

        $sets = $user->favoriteSets()->with('user')->get()->map(function ($set) {
            $data = $set->toArray();
            $data['artist_name'] = $set->user ? $set->user->name : null;
            return $data;
        });

        $favorites = [
            'tracks' => $tracks,
            'sets' => $sets,
            'radios' => $user->favoriteRadios()->get(),
            'djs' => $user->favoriteDJs()->get(),
        ];

        return response()->json($favorites);
    }

    /**
     * @OA\Get(
     *     path="/api/favorites/tracks",
     *     summary="Giriş yapmış kullanıcının favori şarkılarını listeler",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Favori şarkıların listesi",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Track"))
     *     ),
     *     @OA\Response(response=401, description="Yetkisiz")
     * )
     */
    public function tracks(Request $request)
    {
        $tracks = $request->user()->favoriteTracks()->with('user')->get()->map(function ($track) {
            $data = $track->toArray();
            $data['artist_name'] = $track->user ? $track->user->name : null;
            return $data;
        });

        return response()->json($tracks);
    }

    /**
     * @OA\Get(
     *     path="/api/favorites/sets",
     *     summary="Giriş yapmış kullanıcının favori setlerini listeler",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Favori setlerin listesi",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Set"))
     *     ),
     *     @OA\Response(response=401, description="Yetkisiz")
     * )
     */
    public function sets(Request $request)
    {
        $sets = $request->user()->favoriteSets()->with('user')->get()->map(function ($set) {
            $data = $set->toArray();
            $data['artist_name'] = $set->user ? $set->user->name : null;
            return $data;
        });

        return response()->json($sets);
    }

    /**
     * @OA\Get(
     *     path="/api/favorites/radios",
     *     summary="Giriş yapmış kullanıcının favori radyolarını listeler",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Favori radyoların listesi",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Radio"))
     *     ),
     *     @OA\Response(response=401, description="Yetkisiz")
     * )
     */
    public function radios(Request $request)
    {
        return response()->json($request->user()->favoriteRadios()->get());
    }

    /**
     * @OA\Get(
     *     path="/api/favorites/djs",
     *     summary="Giriş yapmış kullanıcının favori DJ'lerini listeler",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Favori DJ'lerin listesi",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(response=401, description="Yetkisiz")
     * )
     */
    public function djs(Request $request)
    {
        return response()->json($request->user()->favoriteDJs()->get());
    }

    private function toggle(Request $request, $model, $relation)
    {
        $user = $request->user();
        $result = $user->$relation()->toggle($model);

        $status = count($result['attached']) > 0 ? 'added' : 'removed';

        return response()->json([
            'message' => 'Favorite status updated successfully.',
            'status' => $status,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/tracks/{track}/toggle-favorite",
     *     summary="Bir şarkıyı favorilere ekler veya çıkarır",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="track", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Favori durumu güncellendi"),
     *     @OA\Response(response=401, description="Yetkisiz"),
     *     @OA\Response(response=404, description="Şarkı bulunamadı")
     * )
     */
    public function toggleTrack(Request $request, Track $track)
    {
        return $this->toggle($request, $track, 'favoriteTracks');
    }

    /**
     * @OA\Post(
     *     path="/api/sets/{set}/toggle-favorite",
     *     summary="Bir seti favorilere ekler veya çıkarır",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="set", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Favori durumu güncellendi"),
     *     @OA\Response(response=401, description="Yetkisiz"),
     *     @OA\Response(response=404, description="Set bulunamadı")
     * )
     */
    public function toggleSet(Request $request, Set $set)
    {
        return $this->toggle($request, $set, 'favoriteSets');
    }

    /**
     * @OA\Post(
     *     path="/api/radios/{radio}/toggle-favorite",
     *     summary="Bir radyoyu favorilere ekler veya çıkarır",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="radio", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Favori durumu güncellendi"),
     *     @OA\Response(response=401, description="Yetkisiz"),
     *     @OA\Response(response=404, description="Radyo bulunamadı")
     * )
     */
    public function toggleRadio(Request $request, Radio $radio)
    {
        return $this->toggle($request, $radio, 'favoriteRadios');
    }

    /**
     * @OA\Post(
     *     path="/api/djs/{dj}/toggle-favorite",
     *     summary="Bir DJ'i favorilere ekler veya çıkarır",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="dj", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Favori durumu güncellendi"),
     *     @OA\Response(response=401, description="Yetkisiz"),
     *     @OA\Response(response=404, description="DJ bulunamadı")
     * )
     */
    public function toggleDj(Request $request, User $dj)
    {
        return $this->toggle($request, $dj, 'favoriteDJs');
    }
}
