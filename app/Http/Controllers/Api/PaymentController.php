<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function generateMaDonHang(): string
    {
        do {
            $ma = 'DH' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (DonHang::where('ma_don_hang', $ma)->exists());

        return $ma;
    }

    public function checkout(Request $request)
    {
        $id_nguoi_dung = Auth::guard('api')->id();
        $ma_don_hang = $this->generateMaDonHang();
        $trang_thai_don_hang = 'Đang xử lý';
        $phuong_thuc_thanh_toan = $request->input('phuong_thuc_thanh_toan');
        $id_dia_chi_nguoi_dung = $request->input('id_dia_chi_nguoi_dung');
        $trang_thai_thanh_toan = 'Chưa thanh toán';
        $ngay_dat_hang = Carbon::now();
        $san_pham = $request->input('san_pham');

        try {
            DB::transaction(function () use (
                $id_nguoi_dung,
                $ma_don_hang,
                $phuong_thuc_thanh_toan,
                $trang_thai_don_hang,
                $ngay_dat_hang,
                $san_pham,
                $trang_thai_thanh_toan,
                $id_dia_chi_nguoi_dung
            ) {
                $id_don_hang = DB::table('don_hang')->insertGetId([
                    'ma_don_hang' => $ma_don_hang,
                    'id_nguoi_dung' => $id_nguoi_dung,
                    'trang_thai_don_hang' => $trang_thai_don_hang,
                    'trang_thai_thanh_toan' => $trang_thai_thanh_toan,
                    'phuong_thuc_thanh_toan' => $phuong_thuc_thanh_toan,
                    'id_dia_chi_nguoi_dung'=> $id_dia_chi_nguoi_dung,
                    'ngay_dat_hang' => $ngay_dat_hang,
                ], 'id_don_hang');

                $data_chi_tiet = [];
                foreach ($san_pham as $item) {
                    $data_chi_tiet[] = [
                        'id_don_hang' => $id_don_hang,
                        'id_san_pham_chi_tiet' => $item['id_san_pham_chi_tiet'],
                        'so_luong' => $item['so_luong'],
                        'don_gia' => $item['don_gia'],
                    ];

                    DB::table('san_pham_chi_tiet')
                        ->where('id_san_pham_chi_tiet', $item['id_san_pham_chi_tiet'])
                        ->decrement('so_luong_ton', $item['so_luong']);
                }

                DB::table('don_hang_chi_tiet')->insert($data_chi_tiet);
                DB::table('gio_hang_chi_tiet')
                    ->join('gio_hang', 'gio_hang_chi_tiet.id_gio_hang', '=', 'gio_hang.id_gio_hang')
                    ->where('id_nguoi_dung', $id_nguoi_dung)
                    ->delete();
            });

            return response()->json([
                'message' => 'Đặt hàng thành công!',
                'ma_don_hang' => $ma_don_hang
            ], 201);

        } catch (\Exception $e) {
            Log::info($e->getMessage());

            return response()->json([
                'message' => 'Đã xảy ra lỗi hệ thống, không thể hoàn tất đơn hàng. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}
