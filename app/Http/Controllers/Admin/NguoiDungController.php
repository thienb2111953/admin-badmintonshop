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
            'email'    => 'required|string|email|max:255|unique:nguoi_dung,email',
            'password' => 'required|string|min:6',
            'ngay_sinh' => 'nullable|date',
            'sdt'       => 'nullable|string|max:12',
        ], [
            'name.required'     => 'Tên người dùng không được để trống.',
            'email.required'    => 'Email không được để trống.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'ngay_sinh.date'     => 'Ngày sinh không hợp lệ.',
            'sdt.max'            => 'Số điện thoại không được vượt quá 12 ký tự.',
        ]);

        $validatedData['email_verified_at'] = now();

        // Hash password trước khi lưu
        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('nguoi_dung')->with('success', 'Tạo người dùng thành công!');
    }

    public function update(Request $request)
    {
        // Lấy user theo id từ request
        $user = User::findOrFail($request->input('id_nguoi_dung'));

        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:nguoi_dung,email,' . $user->id_nguoi_dung . ',id_nguoi_dung',
            'password' => 'nullable|string|min:6',
            'ngay_sinh' => 'nullable|date',
            'sdt'       => 'nullable|string|max:12',
        ], [
            'name.required'     => 'Tên người dùng không được để trống.',
            'email.required'    => 'Email không được để trống.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'ngay_sinh.date'     => 'Ngày sinh không hợp lệ.',
            'sdt.max'            => 'Số điện thoại không được vượt quá 12 ký tự.',
        ]);

        // Hash lại nếu có password mới
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
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
