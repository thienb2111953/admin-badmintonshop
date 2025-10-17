<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SanPhamController extends Controller
{
    public function getProductsDetail(Request $request, $param)
    {
        $query = DB::table('san_pham')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham', '=', 'san_pham.id_san_pham')
            ->join('kho', 'kho.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->join('anh_san_pham', 'anh_san_pham.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->join('mau', 'mau.id_mau', '=', 'san_pham_chi_tiet.id_mau')
            ->join('kich_thuoc', 'kich_thuoc.id_kich_thuoc', '=', 'san_pham_chi_tiet.id_kich_thuoc')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->join('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->join('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.mo_ta',
                'san_pham.gia_niem_yet',
                'san_pham.gia_ban',
                'san_pham.trang_thai',
                'thuong_hieu.ten_thuong_hieu',
                'mau.id_mau',
                'mau.ten_mau',
                'kich_thuoc.id_kich_thuoc',
                'kich_thuoc.ten_kich_thuoc',
                'kho.so_luong_nhap',
                'anh_san_pham.anh_url',
                'anh_san_pham.thu_tu',
            ])
            ->where('san_pham_chi_tiet.slug', $param)
            ->get();

        $result = $query->groupBy('ma_san_pham')->map(function ($items) {
            $first = $items->first();
            return [
                'id_san_pham' => $first->id_san_pham,
                'ma_san_pham' => $first->ma_san_pham,
                'ten_san_pham' => $first->ten_san_pham,
                'mo_ta' => $first->mo_ta,
                'gia_niem_yet' => $first->gia_niem_yet,
                'gia_ban' => $first->gia_ban,
                'trang_thai' => $first->trang_thai,
                'ten_thuong_hieu' => $first->ten_thuong_hieu,
                'mau' => $items->unique('ten_mau')->map(function ($item) {
                    return [
                        'id_mau' => $item->id_mau,
                        'ten_mau' => $item->ten_mau,
                    ];
                })->values(),
                'kich_thuoc' => $items->unique('ten_kich_thuoc')->map(function ($item) {
                    return [
                        'id_kich_thuoc' => $item->id_kich_thuoc,
                        'ten_kich_thuoc' => $item->ten_kich_thuoc,
                    ];
                })->values(),
                'anh_san_pham' => $items->unique('anh_url')->sortBy('thu_tu')->map(function ($item) {
                    return [
                        'anh_url' => env('APP_URL') . '/storage/' . $item->anh_url,
                        'thu_tu' => $item->thu_tu,
                    ];
                })->values()
            ];
        })->first();

        return Response::Success($result, '');
    }
}
