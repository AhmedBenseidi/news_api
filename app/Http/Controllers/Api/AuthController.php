<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * 1. تسجيل الدخول وإصدار التوكن
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        // حذف التوكنات القديمة (اختياري: لضمان وجود جلسة واحدة فقط)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * 2. عرض وتحديث البروفايل (التي طلبتها في ملف الروابط)
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // إذا كان الطلب GET: نعرض البيانات فقط
        if ($request->isMethod('get')) {
            return response()->json($user);
        }

        // إذا كان الطلب PUT: نقوم بالتحديث
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => ['sometimes', 'confirmed', Password::min(8)],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات البروفايل بنجاح',
            'user' => $user
        ]);
    }

    /**
     * 3. تسجيل الخروج وحذف التوكن الحالي
     */
    public function logout(Request $request)
    {
        // استخدام currentAccessToken() بدلاً من id لتجنب خطأ Undefined method
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}
