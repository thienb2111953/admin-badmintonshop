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
    public function cart()
    {
        $userId = Auth::guard('api')->id();

        if (!$userId) {
            return Response::Success([], 'User not authenticated');
        }

        $now = now();

        $data = DB::table('gio_hang')
            ->join('gio_hang_chi_tiet', 'gio_hang_chi_tiet.id_gio_hang', '=', 'gio_hang.id_gio_hang')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')

            ->leftJoin('anh_san_pham', function ($join) {
                $join->on('anh_san_pham.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')
                    ->where('anh_san_pham.thu_tu', '=', 1);
            })

            ->join('san_pham', 'san_pham.id_san_pham', '=', 'san_pham_chi_tiet.id_san_pham')
            ->join('mau', 'mau.id_mau', 'san_pham_chi_tiet.id_mau')
            ->join('kich_thuoc', 'kich_thuoc.id_kich_thuoc', 'san_pham_chi_tiet.id_kich_thuoc')


            ->leftJoinSub(function ($join) use ($now) {
                $join->select('spkm.id_san_pham', 'km.gia_tri', 'km.don_vi_tinh')
                    ->from('san_pham_khuyen_mai as spkm')
                    ->join('khuyen_mai as km', 'spkm.id_khuyen_mai', '=', 'km.id_khuyen_mai')
                    ->where('km.ngay_bat_dau', '<=', $now)
                    ->where('km.ngay_ket_thuc', '>=', $now)
                    ->distinct();
            }, 'active_km', 'san_pham.id_san_pham', '=', 'active_km.id_san_pham')

            ->where('gio_hang.id_nguoi_dung', $userId)
            ->select(
                'san_pham_chi_tiet.id_san_pham_chi_tiet',
                'san_pham_chi_tiet.ten_san_pham_chi_tiet',
                'san_pham_chi_tiet.gia_ban',
                'gio_hang_chi_tiet.so_luong',
                'san_pham_chi_tiet.id_san_pham',
                'san_pham_chi_tiet.id_mau',
                'san_pham_chi_tiet.id_kich_thuoc',
                'san_pham_chi_tiet.so_luong_ton',
                'san_pham.ten_san_pham',
                'gio_hang_chi_tiet.id_gio_hang_chi_tiet',
                'gio_hang_chi_tiet.tong_tien',
                'gio_hang_chi_tiet.so_luong',
                'mau.id_mau',
                'mau.ten_mau',
                'kich_thuoc.id_kich_thuoc',
                'kich_thuoc.ten_kich_thuoc',
                'anh_san_pham.anh_url',

                'active_km.gia_tri as km_gia_tri',
                'active_km.don_vi_tinh as km_don_vi_tinh'
            )
            ->orderBy('san_pham.ten_san_pham')
            ->get();

        return Response::Success($data, 'Cart loaded successfully');
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'id_san_pham' => 'required',
            'id_mau' => 'required',
            'id_kich_thuoc' => 'required',
            'so_luong' => 'required|integer|min:1',
        ], [
            'id_mau.required' => 'Màu sắc không được để trống.',
            'id_san_pham.required' => 'Sản phẩm không được để trống.',
            'id_kich_thuoc.required' => 'Kích thước không được để trống.',
            'so_luong.required' => 'Số lượng không được để trống.',
            'so_luong.integer' => 'Số lượng phải là số nguyên.',
            'so_luong.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $id_nguoi_dung = Auth::guard('api')->id();

        if (!$id_nguoi_dung) {
            return Response::Error('Xác thực không thành công.', '', 401);
        }

        $id_san_pham = $validated['id_san_pham'];
        $id_mau = $validated['id_mau'];
        $id_kich_thuoc = $validated['id_kich_thuoc'];
        $so_luong_them = (int)$validated['so_luong'];

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

        return Response::Success($this->cart(), '');
    }

    public function removeFromCart(Request $request)
    {
        $id_gio_hang_chi_tiet = $request->input('id_gio_hang_chi_tiet');

        $result = DB::table('gio_hang_chi_tiet')->where('id_gio_hang_chi_tiet', $id_gio_hang_chi_tiet)->delete();

        if ($result) {
            return Response::Success($this->cart(), 'Cart removed successfully');
        }
    }

    public function updateQuantity(Request $request)
    {
        $id_gio_hang_chi_tiet = $request->input('id_gio_hang_chi_tiet');
        $so_luong = $request->input('so_luong');

        $item = DB::table('gio_hang_chi_tiet')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')
            ->select('gio_hang_chi_tiet.id_gio_hang_chi_tiet', 'san_pham_chi_tiet.so_luong_ton')
            ->where('gio_hang_chi_tiet.id_gio_hang_chi_tiet', $id_gio_hang_chi_tiet)
            ->first();

        if (!$item) {
            return Response::Error('Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        if ($so_luong > $item->so_luong_ton) {
            return Response::Error('Số lượng yêu cầu vượt quá số lượng tồn kho.');
        }

        $updated = DB::table('gio_hang_chi_tiet')
            ->where('id_gio_hang_chi_tiet', $id_gio_hang_chi_tiet)
            ->update(['so_luong' => $so_luong]);

        if ($updated) {
            return Response::Success($this->cart(), 'Cập nhật số lượng thành công.');
        }

        return Response::Error('Không thể cập nhật số lượng.');
    }


}
