<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\ThuocTinh;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DanhMucController extends Controller
{
    public function index()
    {
        $danh_muc = DanhMuc::with('thuocTinhs')->get(); // tên hàm quan hệ
        $thuoc_tinh = ThuocTinh::all();
        return Inertia::render('admin/danh-muc/danh-muc', [
            'danh_mucs' => $danh_muc,
            'thuoc_tinhs' => $thuoc_tinh,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc'   => 'required|string|max:255',
            'slug'           => 'required|string|max:255',
            'id_thuoc_tinh'  => 'nullable|array',
        ]);

        $danh_muc = DanhMuc::create([
            'ten_danh_muc' => $validated['ten_danh_muc'],
            'slug'         => $validated['slug'],
        ]);

        // gắn thuộc tính vào pivot
        $danh_muc->thuocTinhs()->attach($validated['id_thuoc_tinh']);

        return redirect()->route('danh_muc')->with('success', 'Tạo thành công');
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc'   => 'required|string|max:255',
            'slug'           => 'required|string|max:255',
            'id_thuoc_tinh'  => 'nullable|array',
        ]);

        $danh_muc = DanhMuc::findOrFail($request->id_danh_muc);
        $danh_muc->update([
            'ten_danh_muc' => $validated['ten_danh_muc'],
            'slug'         => $validated['slug'],
        ]);
        // cập nhật pivot (nếu không có id_thuoc_tinh thì gán rỗng)
        $danh_muc->thuocTinhs()->sync($validated['id_thuoc_tinh'] ?? []);

        return redirect()->route('danh_muc')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        $danh_muc = DanhMuc::findOrFail($request->id_danh_muc);
        // xóa pivot trước
        $danh_muc->thuocTinhs()->detach();
        $danh_muc->delete();
        return redirect()->route('danh_muc')->with('success', 'Xóa thành công');
    }
}
