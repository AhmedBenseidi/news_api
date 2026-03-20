<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
{
    $user = $request->user(); // جلب المستخدم صاحب التوكن الحالي

    $data = $request->validate([
        'name' => 'string|max:255',
        'email' => 'email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8|confirmed'
    ]);

    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    }

    $user->update($data);

    return response()->json(['message' => 'تم تحديث ملفك الشخصي بنجاح']);
}
}
