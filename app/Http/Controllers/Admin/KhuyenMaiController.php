<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHangKhuyenMai;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Models\SanPhamKhuyenMai;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KhuyenMaiController extends Controller
{
    public function index()
    {
        $khuyen_mais = KhuyenMai::orderBy('gia_tri', 'asc')->get();
        $san_phams   = SanPham::all();

        $san_pham_khuyen_mais = SanPhamKhuyenMai::query()
            ->selectRaw("
                DISTINCT ON (san_pham_khuyen_mai.id_san_pham)
                san_pham_khuyen_mai.*,
                sp.ten_san_pham,
                km.ma_khuyen_mai,
                spc.gia_ban,
                CASE
                    WHEN km.don_vi_tinh = 'percent'
                        THEN spc.gia_ban - (spc.gia_ban * km.gia_tri / 100)
                    WHEN km.don_vi_tinh = 'fixed'
                        THEN spc.gia_ban - km.gia_tri
                    ELSE spc.gia_ban
                END AS gia_sau_khuyen_mai
            ")
            ->leftJoin('san_pham as sp', 'sp.id_san_pham', '=', 'san_pham_khuyen_mai.id_san_pham')
            ->leftJoin('san_pham_chi_tiet as spc', 'spc.id_san_pham', '=', 'sp.id_san_pham')
            ->leftJoin('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'san_pham_khuyen_mai.id_khuyen_mai')
            ->whereDate('km.ngay_bat_dau', '<=', now())
            ->whereDate('km.ngay_ket_thuc', '>=', now())
            ->orderBy('san_pham_khuyen_mai.id_san_pham')
            ->orderBy('san_pham_khuyen_mai.id_san_pham_khuyen_mai', 'DESC')
            ->get();

        $don_hang_khuyen_mais = DonHangKhuyenMai::query()
            ->leftJoin('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'don_hang_khuyen_mai.id_khuyen_mai')
            ->selectRaw("
                don_hang_khuyen_mai.*,
                km.ma_khuyen_mai,
                CASE
                    WHEN km.don_vi_tinh = 'percent'
                        THEN don_hang_khuyen_mai.gia_tri_duoc_giam - (don_hang_khuyen_mai.gia_tri_duoc_giam * km.gia_tri / 100)
                    WHEN km.don_vi_tinh = 'fixed'
                        THEN don_hang_khuyen_mai.gia_tri_duoc_giam - km.gia_tri
                    ELSE don_hang_khuyen_mai.gia_tri_duoc_giam
                END AS gia_sau_khuyen_mai
            ")
            ->orderBy('gia_tri_duoc_giam', 'asc')
            ->get();

        return Inertia::render('admin/khuyen-mai/khuyen-mai-tabs', [
            'khuyen_mais'          => $khuyen_mais,
            'san_pham_khuyen_mais' => $san_pham_khuyen_mais,
            'san_phams'            => $san_phams,
            'don_hang_khuyen_mais' => $don_hang_khuyen_mais,
        ]);
    }

    public function store(Request $request)
    {
        // Không validate → chỉ convert datetime
        $request->merge([
            'ngay_bat_dau'  => $request->ngay_bat_dau ? date('Y-m-d H:i:s', strtotime($request->ngay_bat_dau)) : null,
            'ngay_ket_thuc' => $request->ngay_ket_thuc ? date('Y-m-d H:i:s', strtotime($request->ngay_ket_thuc)) : null,
        ]);

        KhuyenMai::create($request->all());

        return back()->with('success', 'Thêm khuyến mãi thành công');
    }

    public function update(Request $request)
    {
        $km = KhuyenMai::findOrFail($request->id_khuyen_mai);

        $request->merge([
            'ngay_bat_dau'  => $request->ngay_bat_dau ? date('Y-m-d H:i:s', strtotime($request->ngay_bat_dau)) : null,
            'ngay_ket_thuc' => $request->ngay_ket_thuc ? date('Y-m-d H:i:s', strtotime($request->ngay_ket_thuc)) : null,
        ]);

        $km->update($request->all());

        return back()->with('success', 'Cập nhật khuyến mãi thành công');
    }

    public function destroy(Request $r)
    {
        $km = KhuyenMai::findOrFail($r->id_khuyen_mai);
        $km->delete();

        return back()->with('success', 'Xóa thành công');
    }
}
