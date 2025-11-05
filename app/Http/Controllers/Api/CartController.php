<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GioHang;
use App\Response;
use App\StaticString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required',
            'colorId' => 'required',
            'sizeId' => 'required',
            'quantity' => 'required|integer|min:1',
        ], [
            'productId.required' => 'Sản phẩm không được để trống.',
            'colorId.required' => 'Màu sắc không được để trống.',
            'sizeId.required' => 'Kích thước không được để trống.',
            'quantity.required' => 'Số lượng không được để trống.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $id_nguoi_dung = Auth::guard('api')->id();

        if (!$id_nguoi_dung) {
            return Response::Error('Xác thực không thành công.', '', 401);
        }

        $id_san_pham = $validated['productId'];
        $id_mau = $validated['colorId'];
        $id_kich_thuoc = $validated['sizeId'];
        $so_luong_them = (int)$validated['quantity'];

        $gio_hang = DB::table('gio_hang')->where('id_nguoi_dung', $id_nguoi_dung)->first();

        $id_gio_hang = $gio_hang->id_gio_hang;

        $san_pham_chi_tiet = DB::table('san_pham_chi_tiet')
            ->where('id_mau', $id_mau)
            ->where('id_san_pham', $id_san_pham)
            ->where('id_kich_thuoc', $id_kich_thuoc)
            ->first();

        if (!$san_pham_chi_tiet) {
            return Response::Error('Sản phẩm không tồn tại hoặc đã hết hàng.', '');
        }

        $existing_item = DB::table('gio_hang_chi_tiet')
            ->where('id_gio_hang', $id_gio_hang)
            ->where('id_san_pham_chi_tiet', $san_pham_chi_tiet->id_san_pham_chi_tiet)
            ->first();

        $message = '';
        $result = false;

        if ($existing_item) {
            $new_so_luong = $existing_item->so_luong + $so_luong_them;
            $new_tong_tien = $new_so_luong * $san_pham_chi_tiet->gia_ban;

            $result = DB::table('gio_hang_chi_tiet')
                ->where('id_gio_hang_chi_tiet', $existing_item->id_gio_hang_chi_tiet)
                ->update([
                    'so_luong' => $new_so_luong,
                    'tong_tien' => $new_tong_tien,
                ]);
        } else {
            $result = DB::table('gio_hang_chi_tiet')->insert([
                'id_gio_hang' => $id_gio_hang,
                'id_san_pham_chi_tiet' => $san_pham_chi_tiet->id_san_pham_chi_tiet,
                'so_luong' => $so_luong_them,
                'tong_tien' => $san_pham_chi_tiet->gia_ban * $so_luong_them,
            ]);
        }

        if (!$result) {
            return Response::Error('Lỗi cập nhật giỏ hàng', '');
        }

        $gio_hang_moi = DB::table('gio_hang')
            ->join('gio_hang_chi_tiet', 'gio_hang_chi_tiet.id_gio_hang', '=', 'gio_hang.id_gio_hang')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')
            ->join('san_pham', 'san_pham.id_san_pham', '=', 'san_pham_chi_tiet.id_san_pham')
            ->where('gio_hang.id_nguoi_dung', $id_nguoi_dung)
            ->get();

        return Response::Success($gio_hang_moi, '');
    }

    public function cart(Request $request){
        return DB::table('gio_hang')
            ->join('gio_hang_chi_tiet', 'gio_hang_chi_tiet.id_gio_hang', '=', 'gio_hang.id_gio_hang')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')
            ->where('id_nguoi_dung', Auth::guard('api')->id())->first();
    }
}
