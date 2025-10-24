<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sdt' => $request->sdt,
            'ngay_sinh' => $request->ngay_sinh
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookieExpirationInMinutes = 60 * 24 * 7;

        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'user' => $user,
        ])->cookie(
            'authToken',
            $token,
            $cookieExpirationInMinutes,
            '/',
            null,
            config('session.secure'),
            true,
            false,
            'Lax'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đã đăng xuất thành công']);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return Response::Error('Người dùng chưa đăng nhập', 'Lỗi');
        }

        return Response::Success($user, 'Lấy thông tin người dùng thành công');
    }
}
