<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Update user profile photo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePhoto(Request $request)
    {
        try {
            // Get authenticated user
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı doğrulanamadı'
                ], 401);
            }

            // Check if user has 'user' role
            if (!$user->hasRole('user') && !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu işlem için yetkiniz bulunmuyor'
                ], 403);
            }

            // Validate the uploaded file
            $validator = Validator::make($request->all(), [
                'profile_photo' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:5120' // 5MB max
                ]
            ], [
                'profile_photo.required' => 'Profil fotoğrafı gereklidir',
                'profile_photo.file' => 'Geçerli bir dosya yükleyin',
                'profile_photo.image' => 'Sadece resim dosyaları kabul edilir',
                'profile_photo.mimes' => 'Sadece jpeg, png, jpg, gif formatları kabul edilir',
                'profile_photo.max' => 'Dosya boyutu en fazla 5MB olabilir'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Delete old profile photo if exists
            if ($user->profile_photo) {
                $oldPhotoPath = 'public/' . $user->profile_photo;
                if (Storage::exists($oldPhotoPath)) {
                    Storage::delete($oldPhotoPath);
                }
            }

            // Store the new profile photo
            $file = $request->file('profile_photo');
            $path = $file->store('users/profiles', 'public');

            // Update user profile photo path in database
            $user->profile_photo = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil fotoğrafı başarıyla güncellendi',
                'data' => [
                    'profile_photo_url' => $user->profile_photo_url,
                    'profile_photo_path' => $path
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profil fotoğrafı güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update only profile photo (simplified version)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProfilePhoto(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı doğrulanamadı'
                ], 401);
            }

            // Validate the uploaded file
            $request->validate([
                'profile_photo' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpeg,png,jpg,gif,webp',
                    'max:10240' // 10MB max
                ]
            ], [
                'profile_photo.required' => 'Profil fotoğrafı gereklidir',
                'profile_photo.file' => 'Geçerli bir dosya yükleyin',
                'profile_photo.image' => 'Sadece resim dosyaları kabul edilir',
                'profile_photo.mimes' => 'Sadece jpeg, png, jpg, gif, webp formatları kabul edilir',
                'profile_photo.max' => 'Dosya boyutu en fazla 10MB olabilir'
            ]);

            // Delete old profile photo if exists
            if ($user->profile_photo) {
                $oldPhotoPath = 'public/' . $user->profile_photo;
                if (Storage::exists($oldPhotoPath)) {
                    Storage::delete($oldPhotoPath);
                }
            }

            // Store the new profile photo
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('users/profiles', $filename, 'public');

            // Update user profile photo path in database
            $user->update(['profile_photo' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Profil fotoğrafı başarıyla değiştirildi',
                'data' => [
                    'profile_photo_url' => asset('storage/' . $path),
                    'profile_photo_path' => $path
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profil fotoğrafı güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı doğrulanamadı'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'bio' => $user->bio,
                    'roles' => $user->getRoleNames()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profil bilgileri alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/delete-account",
     *     summary="Delete user account",
     *     description="Permanently delete user account after password verification",
     *     tags={"User"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User's current password for verification",
     *                     example="password123"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Account successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hesap başarıyla silindi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Incorrect password",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Şifre yanlış")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kullanıcı doğrulanamadı")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Doğrulama hatası"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="Şifre gereklidir")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Hesap silinirken hata oluştu"),
     *             @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     *
     * Delete user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı doğrulanamadı'
                ], 401);
            }

            // Validate password
            $validator = Validator::make($request->all(), [
                'password' => 'required|string'
            ], [
                'password.required' => 'Şifre gereklidir'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if password is correct
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Şifre yanlış'
                ], 400);
            }

            // Delete profile photo if exists
            if ($user->profile_photo) {
                $photoPath = 'public/' . $user->profile_photo;
                if (Storage::exists($photoPath)) {
                    Storage::delete($photoPath);
                }
            }

            // Revoke all tokens
            $user->tokens()->delete();

            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hesap başarıyla silindi'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hesap silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
