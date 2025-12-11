<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => [
                'required',
                'string',
                'confirmed',
            ],
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        DB::table('gio_hang')->insert([
            'id_nguoi_dung' => $user['id_nguoi_dung']
        ]);

        return Response::Success($user, 'Đăng ký thành công');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        $newToken = Auth::guard('api')->refresh();
        return $this->respondWithToken($newToken);
    }

    protected function respondWithToken($token)
    {
        $expiresInMinutes = Auth::guard('api')->factory()->getTTL();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresInMinutes * 60,
            'user' => Auth::guard('api')->user(),
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me(Request $request)
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('api')->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
            ],
            'sdt' => ['nullable', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác.',
            'sdt.regex' => 'Số điện thoại không đúng định dạng.',
        ]);

        $user->fill($validatedData);

        if ($user->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thông tin thành công!',
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Không thể lưu vào cơ sở dữ liệu.'
        ], 500);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(16)),
            ]);

            DB::table('gio_hang')->insert([
                'id_nguoi_dung' => $user->id_nguoi_dung ?? $user->id
            ]);

        } else {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        $token = Auth::guard('api')->login($user);

        $frontendUrl = env('GOOGLE_REDIRECT_URI_CLIENT') . 'login/google/callback';

        return redirect($frontendUrl . '?token=' . $token);
    }
}
