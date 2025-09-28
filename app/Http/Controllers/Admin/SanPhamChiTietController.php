<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\DanhMucThuongHieu;
use App\Models\Kho;
use App\Models\KichThuoc;
use App\Models\Mau;
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

        $anh_san_pham = SanPhamChiTiet::with(['anhSanPham', 'mau'])
            ->where('id_san_pham', $id_san_pham)
            ->get()
            ->groupBy(fn($item) => $item->mau->ten_mau ?? 'Không rõ')
            ->map(function ($items) {
                return [
                    'id_san_pham_chi_tiet' => $items->first()->id_san_pham_chi_tiet,
                    'ten_mau' => $items->first()->mau->ten_mau ?? null,
                    'path_anh_san_pham_old' => $items->flatMap(function ($item) {
                        return $item->anhSanPham->map(function ($anh) {
                            return [
                                'id_anh_san_pham' => $anh->id_anh_san_pham,
                                'id_san_pham_chi_tiet' => $anh->id_san_pham_chi_tiet,
                                'anh_url' => $anh->anh_url,
                                'thu_tu' => $anh->thu_tu,
                            ];
                        });
                    })->values()->toArray(),
                ];
            })
            ->values();

        // $san_pham_chi_tiet = SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get();

        $san_pham_chi_tiet = SanPhamChiTiet::with(['mau', 'kichThuoc', 'kho'])
            ->where('id_san_pham', $id_san_pham)
            ->get();

        return Inertia::render('admin/san-pham-chi-tiet/san-pham-chi-tiet', [
            'maus' => Mau::all(),
            'kich_thuocs' => KichThuoc::all(),
            'san_pham_info' => $sanPham,
            'anh_san_phams' => $anh_san_pham,
            'san_pham_chi_tiets' => $san_pham_chi_tiet,
        ]);
    }

    public function store(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'id_mau'              => 'required|integer',
            'id_kich_thuoc'       => 'required|integer',
            'so_luong_nhap'         => 'required|integer',
            'ngay_nhap'         => 'required|date',
        ], [
            'id_kich_thuoc.required' => 'Không để trống kích thước',
            'id_mau.required' => 'Không để trống màu',
            'so_luong_nhap.required' => 'Không để trống số lượng tồn',
            'ngay_nhap.required' => 'Không để trống ngày nhập',
        ]);

        // 1. Tạo chi tiết sản phẩm
        $chiTiet = SanPhamChiTiet::create([
            'id_san_pham'   => $id_san_pham,
            'id_mau'        => $validated['id_mau'],
            'id_kich_thuoc' => $validated['id_kich_thuoc'],
            'so_luong_nhap'  => $validated['so_luong_nhap'],
        ]);

        // 2. Thêm bản ghi vào bảng kho
        Kho::create([
            'id_san_pham_chi_tiet' => $chiTiet->id_san_pham_chi_tiet,
            'so_luong_nhap'        => $validated['so_luong_nhap'],
            'ngay_nhap'            => $validated['ngay_nhap'],
        ]);

        return redirect()->back()->with('success', 'Thêm sản phẩm chi tiết thành công');
    }


    public function update(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'id_san_pham_chi_tiet' => 'required|integer',
            'id_mau'               => 'required|integer',
            'id_kich_thuoc'        => 'required|integer',
            'so_luong_nhap'        => 'required|integer',
            'ngay_nhap'            => 'required|date',
        ], [
            'id_san_pham_chi_tiet.required' => 'Không tìm thấy sản phẩm chi tiết',
            'id_kich_thuoc.required'        => 'Không để trống kích thước',
            'id_mau.required'               => 'Không để trống màu',
            'so_luong_nhap.required'        => 'Không để trống số lượng tồn',
            'ngay_nhap.required'            => 'Không để trống ngày nhập',
        ]);

        // 1. Update chi tiết sản phẩm
        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($validated['id_san_pham_chi_tiet']);
        $san_pham_chi_tiet->update([
            'id_san_pham'   => $id_san_pham,
            'id_mau'        => $validated['id_mau'],
            'id_kich_thuoc' => $validated['id_kich_thuoc'],
        ]);

        // 2. Update bản ghi kho (giả sử mỗi chi tiết có 1 bản ghi trong kho)
        $kho = Kho::where('id_san_pham_chi_tiet', $san_pham_chi_tiet->id_san_pham_chi_tiet)->first();

        $kho->update([
            'so_luong_nhap' => $validated['so_luong_nhap'],
            'ngay_nhap'     => $validated['ngay_nhap'],
        ]);

        return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
    }



    public function destroy(Request $request, $id_san_pham)
    {
        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham_chi_tiet->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }
}
