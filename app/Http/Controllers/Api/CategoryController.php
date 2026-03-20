<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * جلب جميع الأقسام مع عدد الأخبار في كل قسم
     */
    public function index()
    {
        // استخدام withCount لجلب عدد الأخبار المرتبطة بكل قسم (مفيد جداً للواجهات)
        $categories = Category::withCount('news')->get();
        return response()->json($categories);
    }

    /**
     * إنشاء قسم جديد وتوليد الـ Slug تلقائياً
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']), // توليد السلاج تلقائياً من الاسم
        ]);

        return response()->json([
            'message' => 'تم إنشاء القسم بنجاح',
            'data' => $category
        ], 201);
    }

    /**
     * تحديث القسم
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']), // تحديث السلاج إذا تغير الاسم
        ]);

        return response()->json([
            'message' => 'تم تحديث القسم بنجاح',
            'data' => $category
        ]);
    }

    /**
     * حذف القسم مع التحقق من وجود أخبار مرتبطة
     */
    public function destroy($id)
    {
        $category = Category::withCount('news')->findOrFail($id);

        // حماية: منع حذف القسم إذا كان يحتوي على أخبار
        if ($category->news_count > 0) {
            return response()->json([
                'message' => 'لا يمكن حذف القسم لأنه يحتوي على أخبار مرتبطة به. قم بنقل الأخبار أولاً.'
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'تم حذف القسم بنجاح']);
    }
}
