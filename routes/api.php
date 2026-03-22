<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    HomeController,
    NewsController,
    AuthController,
    BannerController,
    CategoryController
};

/*
|--------------------------------------------------------------------------
| Public Routes (المسارات العامة - متاحة للجميع بدون Token)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/search', [NewsController::class, 'search']);

// مسارات العرض العامة (Index & Show)
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// مسارات الأخبار العامة (العرض)
Route::prefix('news')->controller(NewsController::class)->group(function () {
    Route::get('/', 'index');           // عرض كل الأخبار
    Route::get('/{id}', 'show');        // عرض خبر محدد
    Route::get('/category/{id}', 'getByCategory'); // عرض أخبار حسب التصنيف
});

/*
|--------------------------------------------------------------------------
| Protected Routes (المسارات المحمية - تتطلب Token)
|--------------------------------------------------------------------------
| هذه المسارات مخصصة فقط لعمليات الإضافة، التعديل، والحذف.
*/
Route::middleware('auth:sanctum')->group(function () {

    // إدارة الحساب
    Route::get('/user', fn(Request $request) => $request->user());
    Route::match(['get', 'put'], '/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // العمليات الإدارية (Store, Update, Destroy)
    Route::apiResource('news', NewsController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('banners', BannerController::class)->only(['store', 'update', 'destroy']);
});
