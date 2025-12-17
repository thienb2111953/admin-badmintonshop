<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
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


    public function inHoaDon($id_don_hang)
    {
        $rows = DB::table('don_hang as dh')
            ->join('don_hang_chi_tiet as dhct', 'dhct.id_don_hang', '=', 'dh.id_don_hang')
            ->join('san_pham_chi_tiet as spct', 'spct.id_san_pham_chi_tiet', '=', 'dhct.id_san_pham_chi_tiet')
            ->join('san_pham as sp', 'sp.id_san_pham', '=', 'spct.id_san_pham')
            ->leftJoin('mau as m', 'm.id_mau', '=', 'spct.id_mau')
            ->leftJoin('kich_thuoc as kt', 'kt.id_kich_thuoc', '=', 'spct.id_kich_thuoc')
            ->leftJoin('dia_chi_nguoi_dung as dc', function ($join) {
                $join->on('dc.id_nguoi_dung', '=', 'dh.id_nguoi_dung')
                    ->where('dc.mac_dinh', true);
            })
            ->where('dh.id_don_hang', $id_don_hang)
            ->select([
                'dh.id_don_hang',
                'dh.ma_don_hang',
                'dh.trang_thai_don_hang',
                'dh.phuong_thuc_thanh_toan',
                'dh.trang_thai_thanh_toan',
                DB::raw("TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM-DD HH24:MI') as ngay_dat_hang"),

                'dc.ten_nguoi_dung',
                'dc.so_dien_thoai',
                'dc.dia_chi',

                'sp.ten_san_pham',
                'm.ten_mau',
                'kt.ten_kich_thuoc',
                'dhct.so_luong',
                'dhct.don_gia',
            ])
            ->get();

        if ($rows->isEmpty()) abort(404);

        $first = $rows->first();

        $tongTien = $rows->sum(function ($r) {
            return $r->don_gia * $r->so_luong;
        });

        $orderData = [
            'id_don_hang' => $first->id_don_hang,
            'ma_don_hang' => $first->ma_don_hang,
            'ngay_dat_hang' => $first->ngay_dat_hang,
            'trang_thai_don_hang' => $first->trang_thai_don_hang,
            'phuong_thuc_thanh_toan' => $first->phuong_thuc_thanh_toan,
            'trang_thai_thanh_toan' => $first->trang_thai_thanh_toan,

            'tong_tien' => (int) $tongTien,

            'dia_chi_giao_hang' => [
                'ten_nguoi_dung' => (string) $first->ten_nguoi_dung,
                'so_dien_thoai' => (string) $first->so_dien_thoai,
                'dia_chi' => (string) $first->dia_chi,
            ],

            'san_pham' => $rows->map(function ($r) {
                return [
                    'ten_san_pham' => $r->ten_san_pham,
                    'mau' => $r->ten_mau,
                    'kich_thuoc' => $r->ten_kich_thuoc,
                    'don_gia' => (int) $r->don_gia,
                    'so_luong' => (int) $r->so_luong,
                    'thanh_tien' => (int) ($r->don_gia * $r->so_luong),
                ];
            })->values(),
        ];

        return view('invoices-print', compact('orderData'));
    }
}
