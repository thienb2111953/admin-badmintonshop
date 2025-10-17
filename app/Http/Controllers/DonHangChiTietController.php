<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DonHangChiTietController extends Controller
{
    public function index($id_don_hang)
    {
        $donHang = DB::table('don_hang')->where('id_don_hang', $id_don_hang)->first();

        $chiTiet = DB::table('don_hang_chi_tiet')
        ->join('don_hang', 'don_hang_chi_tiet.id_don_hang', '=', 'don_hang.id_don_hang')
        ->join('san_pham_chi_tiet', 'don_hang_chi_tiet.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
        ->select(
            'don_hang_chi_tiet.*',
            'don_hang.ma_don_hang',
            'don_hang.trang_thai',
            'don_hang.ngay_dat_hang',
            'san_pham_chi_tiet.ten_san_pham_chi_tiet'
        )
        ->where('don_hang.id_don_hang', $id_don_hang) 
        ->get();

        return Inertia::render('admin/don-hang-chi-tiet/don-hang-chi-tiet', [
            'don_hang_info' => $donHang,
            'don_hang_chi_tiets' => $chiTiet,
            // 'san_pham_chi_tiets' => SanPhamChiTiet::all(),
        ]);
    }
}
