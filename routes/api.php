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
| Public Routes (المسارات العامة)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/search', [NewsController::class, 'search']);

// مجموعة مسارات الأخبار العامة
Route::prefix('news')->controller(NewsController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::get('/category/{id}', 'getByCategory'); // تحسين الرابط ليكون أوضح
});

/*
|--------------------------------------------------------------------------
| Protected Routes (المسارات المحمية - تتطلب Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // إدارة الحساب
    Route::get('/user', fn(Request $request) => $request->user());
    Route::match(['get', 'put'], '/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']); // نقل المنطق للكنترولر أفضل

    // إدارة الأخبار (Resourceful)
    Route::apiResource('news', NewsController::class)->only(['store', 'update', 'destroy']);

    // إدارة التصنيفات
    Route::apiResource('categories', CategoryController::class);

    // إدارة البنرات
    Route::apiResource('banners', BannerController::class)->except(['show',]);
});
