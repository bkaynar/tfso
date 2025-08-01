<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            if (!$user->hasRole('user')) {
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
}