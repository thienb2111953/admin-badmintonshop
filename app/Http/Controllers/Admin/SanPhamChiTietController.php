<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\DanhMucThuongHieu;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SanPhamChiTietController extends Controller
{
    public function index($id_san_pham)
    {
        $sanPham = SanPham::with('danhMucThuongHieu')->find($id_san_pham);

        $anh_san_pham = SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get()
            ->groupBy('ten_mau')
            ->map(function ($items) {
                return [
                    'id_san_pham_chi_tiet' => $items->first()->id_san_pham_chi_tiet,
                    'ten_mau' => $items->first()->ten_mau,
                    // 'ten_kich_thuoc' => $items->pluck('ten_kich_thuoc')->implode(', '), // chuỗi
                    // 'ten_kich_thuoc' => $items->pluck('ten_kich_thuoc')->toArray(), // mảng
                    'files_anh_san_pham' => $items->flatMap(function ($item) {
                        return $item->anhSanPham->pluck('anh_url');
                    })->values()->toArray(),
                ];
            })
            ->values();

        $san_pham_chi_tiet = SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get();

        return Inertia::render('admin/san-pham-chi-tiet/san-pham-chi-tiet', [
            'san_pham_info' => $sanPham,
            'anh_san_phams' => $anh_san_pham,
            'san_pham_chi_tiets' => $san_pham_chi_tiet,
        ]);
    }

    public function store(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'ten_kich_thuoc'       => 'nullable|string',
            'so_luong_ton'         => 'required|integer',
        ], [
            'so_luong_ton.required' => 'Không để trống số lượng tồn',
        ]);

        SanPhamChiTiet::create([
            'id_san_pham'   => $id_san_pham,
            'ten_mau'       => $validated['ten_mau'] ?? null,
            'ten_kich_thuoc' => $validated['ten_kich_thuoc'] ?? null,
            'so_luong_ton' => $validated['so_luong_ton'],
        ]);

        return redirect()->back()->with('success', 'Thêm sản phẩm chi tiết thành công');
    }


    public function update(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'ten_kich_thuoc'       => 'nullable|string',
            'so_luong_ton'         => 'required|integer',
        ], [
            'so_luong_ton.required' => 'Không để trống số lượng tồn',
        ]);

        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham_chi_tiet->update($validated);

        return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
    }


    public function destroy(Request $request, $id_san_pham)
    {
        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham_chi_tiet->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }

   
}
