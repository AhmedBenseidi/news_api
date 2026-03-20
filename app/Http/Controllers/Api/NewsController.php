<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    /**
     * 1. جلب قائمة الأخبار مع التصفح
     * تحسين: جلب الحقول المطلوبة فقط من الجداول المرتبطة لتسريع الاستعلام.
     */
    public function index()
    {
        $news = News::with([
            'category:id,name',
            'author:id,name'
        ])
        ->latest()
        ->paginate(15);

        return response()->json($news);
    }

    /**
     * 2. إضافة خبر جديد مع رفع الصورة لـ ImgBB
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'required|image|max:5120', // 5MB Max
        ]);

        try {
            // رفع الصورة باستخدام Stream (أفضل للأداء)
            $response = Http::asMultipart()
                ->post('https://api.imgbb.com/1/upload?key=' . env('IMGBB_API_KEY'), [
                    'image' => fopen($request->file('image')->path(), 'r'),
                ]);

            if (!$response->successful()) {
                throw new \Exception('فشل الاتصال بـ ImgBB');
            }

            $imageUrl = $response->json()['data']['url'];

            // إنشاء الخبر
            $news = News::create([
                'title'       => $validated['title'],
                'slug'        => Str::slug($validated['title']) . '-' . Str::random(5),
                'content'     => $validated['content'],
                'category_id' => $validated['category_id'],
                'user_id'     => auth()->id(),
                'image_url'   => $imageUrl,
                'views'       => 0,
            ]);

            return response()->json([
                'message' => 'تم نشر الخبر بنجاح!',
                'data'    => $news->load('category:id,name')
            ], 201);

        } catch (\Exception $e) {
            Log::error("خطأ في رفع الصورة أو حفظ الخبر: " . $e->getMessage());
            return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب'], 500);
        }
    }

    /**
     * 3. عرض خبر محدد مع زيادة المشاهدات
     */
    public function show($id)
    {
        $article = News::with(['category:id,name', 'author:id,name'])->find($id);

        if (!$article) {
            return response()->json(['message' => 'الخبر غير موجود'], 404);
        }

        // زيادة المشاهدات
        $article->increment('views');

        return response()->json($article);
    }

    /**
     * 4. جلب أخبار قسم معين
     */
    public function getByCategory($categoryId)
    {
        $news = News::where('category_id', $categoryId)
                    ->with('category:id,name')
                    ->latest()
                    ->paginate(10);

        return response()->json($news);
    }

    /**
     * 5. البحث المتقدم
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $results = News::where('title', 'LIKE', "%{$query}%")
                       ->orWhere('content', 'LIKE', "%{$query}%")
                       ->with('category:id,name')
                       ->latest()
                       ->limit(20)
                       ->get();

        return response()->json($results);
    }

    /**
     * 6. تحديث الخبر
     * تحسين: إضافة حماية لضمان أن صاحب الخبر فقط هو من يعدله.
     */
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        // التحقق من الصلاحية (Security Policy)
        if ($news->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا الخبر'], 403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'content'     => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        // تحديث الـ slug إذا تغير العنوان
        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        $news->update($validated);

        return response()->json([
            'message' => 'تم تحديث الخبر بنجاح',
            'data'    => $news
        ]);
    }

    /**
     * 7. حذف الخبر
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // التحقق من الصلاحية
        if ($news->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بحذف هذا الخبر'], 403);
        }

        $news->delete();

        return response()->json(['message' => 'تم حذف الخبر بنجاح']);
    }
}
