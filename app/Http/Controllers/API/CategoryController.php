<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/categories/bulk",
     * summary="Toplu kategori ekleme işlemi",
     * tags={"Categories"},
     * @OA\RequestBody(
     * required=true,
     * description="Eklenmek istenen kategorilerin listesi",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(
     * property="categories",
     * type="array",
     * description="Kategori objelerinin listesi",
     * @OA\Items(
     * type="object",
     * @OA\Property(
     * property="name",
     * type="string",
     * description="Kategori adı",
     * example="Pop Müzik"
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Kategoriler başarıyla eklendi",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Kategoriler başarıyla eklendi."),
     * @OA\Property(property="count", type="integer", example=3)
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Doğrulama hatası",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Doğrulama hatası"),
     * @OA\Property(
     * property="errors",
     * type="object",
     * example={"categories.0.name": {"The categories.0.name field is required."}}
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Sunucu hatası",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Kategoriler eklenirken bir hata oluştu."),
     * @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
     * )
     * )
     * )
     */
    public function storeBulk(Request $request)
    {
        // Gelen verinin bir dizi olduğundan emin olmak için doğrulama
        // Her bir kategori nesnesinin sadece 'name' alanını içerdiğini kontrol ediyoruz.
        try {
            $validator = Validator::make($request->all(), [
                'categories' => 'required|array', // 'categories' anahtarının zorunlu ve dizi olmasını sağlar
                'categories.*.name' => 'required|string|max:255', // Dizideki her bir elemanın 'name' alanı
                // 'image' alanı artık gerekli değil, bu yüzden doğrulama kuralından çıkarıldı.
            ]);

            $validator->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422); // HTTP 422 Unprocessable Entity
        }

        $categories = $request->input('categories');

        // Toplu ekleme işlemi için Category::insert() kullanıyoruz.
        // Bu, her bir model için ayrı ayrı kaydetme çağrısı yapmaktan çok daha verimlidir.
        try {
            Category::insert($categories); // Veritabanına toplu ekleme
            return response()->json([
                'message' => 'Kategoriler başarıyla eklendi.',
                'count' => count($categories)
            ], 201); // HTTP 201 Created
        } catch (\Exception $e) {
            // Veritabanı ekleme sırasında oluşabilecek hataları yakala
            return response()->json([
                'message' => 'Kategoriler eklenirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500); // HTTP 500 Internal Server Error
        }
    }
}
