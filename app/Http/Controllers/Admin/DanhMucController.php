<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DanhMucController extends Controller
{
    public function index()
    {
        $danh_muc = DanhMuc::all();
        return Inertia::render('admin/danh-muc/danh-muc', [
            'danh_mucs' => $danh_muc,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);
        DanhMuc::create([
            ...$validated,
            // id_user => auth()->id('id_user'),
        ]);
        return redirect()->route('danh_muc')->with('success', 'Tạo thành công');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $danh_muc = DanhMuc::findOrFail($request->id_danh_muc);

        $danh_muc->update($validated);
        return redirect()->route('danh_muc')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        $danh_muc = DanhMuc::findOrFail($request->id_danh_muc);

        $danh_muc->delete();
        return redirect()->route('danh_muc')->with('success', 'Xóa thành công');
    }
}
