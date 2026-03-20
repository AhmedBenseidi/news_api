<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    /**
     * جلب كل البنرات المفعلة (للعرض في التطبيق)
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)
                         ->latest()
                         ->get();

        return response()->json($banners);
    }

    /**
     * إضافة بنر جديد مع رفع الصورة لـ ImgBB
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:5120', // حد أقصى 5 ميجا
            'link'  => 'nullable|url',
        ]);

        try {
            // رفع الصورة باستخدام Stream (أسرع من base64 وأقل استهلاكاً للرام)
            $response = Http::asMultipart()
                ->post('https://api.imgbb.com/1/upload?key=' . env('IMGBB_API_KEY'), [
                    'image' => fopen($request->file('image')->path(), 'r'),
                ]);

            if (!$response->successful()) {
                throw new \Exception('فشل الاتصال بـ ImgBB');
            }

            $imageUrl = $response->json()['data']['url'];

            $banner = Banner::create([
                'title'      => $validated['title'],
                'image_path' => $imageUrl,
                'link'       => $validated['link'],
                'is_active'  => true,
            ]);

            return response()->json([
                'message' => 'تم رفع البنر بنجاح!',
                'data'    => $banner
            ], 201);

        } catch (\Exception $e) {
            Log::error("خطأ في رفع البنر: " . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء رفع صورة البنر'], 500);
        }
    }

    /**
     * تحديث بيانات البنر (العنوان أو الرابط أو حالة التفعيل)
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'link'      => 'nullable|url',
            'is_active' => 'sometimes|boolean',
        ]);

        // ملاحظة: إذا أردت تحديث الصورة أيضاً، يمكنك إضافة منطق الرفع هنا كما في store
        $banner->update($validated);

        return response()->json([
            'message' => 'تم تحديث البنر بنجاح',
            'data'    => $banner
        ]);
    }

    /**
     * حذف البنر نهائياً
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(['message' => 'تم حذف البنر بنجاح']);
    }

    /**
     * تبديل حالة البنر (تفعيل/تعطيل) بسرعة
     */
    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'message' => $banner->is_active ? 'تم تفعيل البنر' : 'تم تعطيل البنر',
            'is_active' => $banner->is_active
        ]);
    }
}
