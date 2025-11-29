<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function getOrders()
    {
        $id_nguoi_dung = Auth::guard('api')->id();

        $data = DB::table('don_hang')
            ->join('don_hang_chi_tiet', 'don_hang.id_don_hang', '=', 'don_hang_chi_tiet.id_don_hang')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'don_hang_chi_tiet.id_san_pham_chi_tiet')
            ->join('san_pham', 'san_pham.id_san_pham', '=', 'san_pham_chi_tiet.id_san_pham')
            ->join('anh_san_pham', 'anh_san_pham.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->leftJoin('mau', 'mau.id_mau', '=', 'san_pham_chi_tiet.id_mau')
            ->leftJoin('kich_thuoc', 'kich_thuoc.id_kich_thuoc', '=', 'san_pham_chi_tiet.id_kich_thuoc')
            ->where('don_hang.id_nguoi_dung', $id_nguoi_dung)
            ->where('anh_san_pham.thu_tu', 1)
            ->select(
                'don_hang.id_don_hang',
                'don_hang.ma_don_hang',
                'don_hang.tong_tien',
                'don_hang.trang_thai_don_hang',
                'don_hang.created_at as ngay_dat',
                'san_pham.ten_san_pham',
                'san_pham_chi_tiet.id_san_pham_chi_tiet',
                'san_pham_chi_tiet.gia_ban',
                'don_hang_chi_tiet.so_luong',
                'anh_san_pham.anh_url',
                'mau.ten_mau',
                'kich_thuoc.ten_kich_thuoc'
            )
            ->get();

        $groupedOrders = $data->groupBy('id_don_hang')->map(function ($items) {
            $orderInfo = $items->first();

            return [
                'id_don_hang' => $orderInfo->id_don_hang,
                'ma_don_hang' => $orderInfo->ma_don_hang,
                'tong_tien' => $orderInfo->tong_tien,
                'trang_thai_don_hang' => $orderInfo->trang_thai_don_hang,
                'ngay_dat' => Carbon::parse($orderInfo->ngay_dat)->format('d/m/Y'),
                'chi_tiet' => $items->map(function ($item) {
                    return [
                        'id_san_pham_chi_tiet'=> $item->id_san_pham_chi_tiet,
                        'ten_san_pham' => $item->ten_san_pham,
                        'gia_ban' => $item->gia_ban,
                        'so_luong' => $item->so_luong,
                        'anh_url' => Storage::disk('public')->url($item->anh_url),
                        'mau' => $item->ten_mau,
                        'kich_thuoc' => $item->ten_kich_thuoc,
                    ];
                })->values()
            ];
        })->values();

        return Response::Success($groupedOrders, 'Lấy danh sách đơn hàng thành công');
    }
}
