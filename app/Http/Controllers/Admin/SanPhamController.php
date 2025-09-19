<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucThuongHieu;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SanPhamController extends Controller
{
    public function index($id_danh_muc_thuong_hieu)
    {
        $info_dmth = DanhMucThuongHieu::find($id_danh_muc_thuong_hieu);
        $san_phams = SanPham::where('id_danh_muc_thuong_hieu', $id_danh_muc_thuong_hieu)->get();

        return Inertia::render('admin/san-pham/san-pham', [
            'info_dmth' => $info_dmth,
            'san_phams' => $san_phams
        ]);
    }

    public function store(Request $request, $id_danh_muc_thuong_hieu)
    {
        $validated = $request->validate([
            'ma_san_pham' => 'required|string|max:255',
            'ten_san_pham' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia_niem_yet' => 'nullable|integer|min:0|max:999999999999',
            'gia_ban'      => 'nullable|integer|min:0|max:999999999999',
        ], [
            'ma_san_pham.required' => 'Mã sản phẩm không được để trống',
            'ten_san_pham.required' => 'Tên sản phẩm không được để trống',
            'gia_niem_yet.min' => 'Giá niêm yết không nhỏ hơn 0',
            'gia_ban.min' => 'Giá bán không nhỏ hơn 0',
        ]);

        $validated['id_danh_muc_thuong_hieu'] = $id_danh_muc_thuong_hieu;

        SanPham::create($validated);

        return redirect()->back()->with('success', 'Thêm thành công');
    }

    public function update(Request $request, $id_danh_muc_thuong_hieu)
    {
        $validated = $request->validate([
            'ma_san_pham' => 'required|string|max:255',
            'ten_san_pham' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia_niem_yet' => 'nullable|integer|min:0|max:999999999999',
            'gia_ban'      => 'nullable|integer|min:0|max:999999999999',
        ], [
            'ma_san_pham.required' => 'Mã sản phẩm không được để trống',
            'ten_san_pham.required' => 'Tên sản phẩm không được để trống',
            'gia_niem_yet.min' => 'Giá niêm yết không nhỏ hơn 0',
            'gia_ban.min' => 'Giá bán không nhỏ hơn 0',
        ]);

        $validated['id_danh_muc_thuong_hieu'] = $id_danh_muc_thuong_hieu;

        $san_pham = SanPham::findOrFail($request->id_san_pham);

        $san_pham->update($validated);

        return redirect()->back()->with('success', 'Cập nhật thành công');
    }

    public function destroy(Request $request, $id_danh_muc_thuong_hieu)
    {
        $san_pham = SanPham::findOrFail($request->id_san_pham);

        $san_pham->delete();

        return redirect()->back()->with('success', 'Xóa thành công');
    }
}
