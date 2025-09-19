<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuocTinh;
use App\Models\ThuocTinhChiTiet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThuocTinhChiTietController extends Controller
{
    public function index($id_thuoc_tinh)
    {
        return Inertia::render('admin/thuoc-tinh-chi-tiet/thuoc-tinh-chi-tiet', [
            'thuoc_tinh_info' => ThuocTinh::find($id_thuoc_tinh),
            'id_thuoc_tinh' => $id_thuoc_tinh,
            'thuoc_tinh_chi_tiets' => ThuocTinhChiTiet::where('id_thuoc_tinh', $id_thuoc_tinh)->get(),
        ]);
    }

    public function store(Request $request, $id_thuoc_tinh)
    {
        $validated = $request->validate([
            'ten_thuoc_tinh_chi_tiet' => 'required|string|max:255',
        ], [
            'ten_thuoc_tinh_chi_tiet.required' => 'Tên thuộc tính chi tiết không được để trống'
        ]);

        $validated['id_thuoc_tinh'] = $id_thuoc_tinh;

        ThuocTinhChiTiet::create($validated);

        return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
    }

    public function update(Request $request, $id_thuoc_tinh)
    {
        $validated = $request->validate([
            'ten_thuoc_tinh_chi_tiet' => 'required|string|max:255',
        ], [
            'ten_thuoc_tinh_chi_tiet.required' => 'Tên thuộc tính chi tiết không được để trống'
        ]);

        $validated['id_thuoc_tinh'] = $id_thuoc_tinh;

        $thuoc_tinh = ThuocTinhChiTiet::findOrFail($request->id_thuoc_tinh_chi_tiet);

        $thuoc_tinh->update($validated);

        return redirect()->back()->with('success', 'Cập nhật chi tiết thành công');
    }

    public function destroy(Request $request, $id_thuoc_tinh)
    {
        $thuoc_tinh = ThuocTinhChiTiet::findOrFail($request->id_thuoc_tinh_chi_tiet);

        $thuoc_tinh->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }
}
