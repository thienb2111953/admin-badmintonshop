<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NguoiDungController extends Controller
{
    public function index()
    {
        $users = User::all();

        return Inertia::render('admin/nguoi-dung/nguoi-dung', [
            'users' => $users,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Tên người dùng không được để trống.',
            'email.required'    => 'Email không được để trống.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min'      => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Hash password trước khi lưu
        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('nguoi_dung')->with('success', 'Tạo người dùng thành công!');
    }


    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'sdt'      => 'nullable|string|max:20',
            'ngay_sinh' => 'nullable|date',
        ], [
            'name.required'     => 'Tên người dùng không được để trống.',
            'email.required'    => 'Email không được để trống.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã tồn tại.',
            'password.min'      => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Nếu có mật khẩu mới thì hash lại
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']); // không update password nếu để trống
        }

        $user->update($validatedData);

        return redirect()->route('nguoi_dung')->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id_nguoi_dung);
        $user->delete();

        return redirect()->route('nguoi_dung')->with('success', 'Xóa người dùng thành công!');
    }
}
