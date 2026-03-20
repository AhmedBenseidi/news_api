<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\News;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * جلب بيانات الصفحة الرئيسية في رد واحد
     */
    public function index()
    {
        // استخدام التحميل المسبق (Eager Loading) وتحديد الحقول المطلوبة لتقليل حجم الرد
        return response()->json([
            'status' => 'success',

            // 1. البنرات النشطة (الأحدث أولاً)
            'banners' => Banner::where('is_active', true)
                               ->select('id', 'title', 'image_path', 'link')
                               ->latest()
                               ->get(),

            // 2. الأقسام (مع عدد الأخبار في كل قسم)
            'categories' => Category::withCount('news')
                                    ->select('id', 'name', 'slug')
                                    ->get(),

            // 3. الأخبار الأكثر مشاهدة (الترند)
            'trending_news' => News::with('category:id,name')
                                   ->select('id', 'title', 'image_url', 'category_id', 'views', 'created_at')
                                   ->orderBy('views', 'desc')
                                   ->take(5)
                                   ->get(),

            // 4. أحدث الأخبار
            'recent_news' => News::with('category:id,name')
                                 ->select('id', 'title', 'image_url', 'category_id', 'created_at')
                                 ->latest()
                                 ->take(10)
                                 ->get(),
        ]);
    }
}
