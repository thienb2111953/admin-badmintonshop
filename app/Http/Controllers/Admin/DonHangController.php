<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DonHangController extends Controller
{
    public function index()
    {
        $donHangs = DB::table('don_hang')
            ->leftJoin('dia_chi_nguoi_dung', 'dia_chi_nguoi_dung.id_dia_chi_nguoi_dung', '=', 'don_hang.id_dia_chi_nguoi_dung')
            ->leftJoin('nguoi_dung', 'nguoi_dung.id_nguoi_dung', '=', 'dia_chi_nguoi_dung.id_nguoi_dung')
            ->leftJoin('don_hang_chi_tiet', 'don_hang.id_don_hang', '=', 'don_hang_chi_tiet.id_don_hang')
            ->select(
                'don_hang.*',
                'dia_chi_nguoi_dung.dia_chi',
                'dia_chi_nguoi_dung.so_dien_thoai',
                DB::raw("CONCAT(nguoi_dung.name, ' (', nguoi_dung.email, ')') as nguoi_dung_thong_tin"),
            )
            ->groupBy(
                'don_hang.id_don_hang',
                'don_hang.tong_tien',
                'don_hang.ma_don_hang',
                'don_hang.trang_thai_don_hang',
                'don_hang.trang_thai_thanh_toan',
                'don_hang.phuong_thuc_thanh_toan',
                'don_hang.created_at',
                'don_hang.updated_at',
                'nguoi_dung.name',
                'nguoi_dung.email',
                'dia_chi_nguoi_dung.dia_chi',
                'dia_chi_nguoi_dung.so_dien_thoai',
            )
            ->orderBy('id_don_hang', 'desc')
            ->get();

        return Inertia::render('admin/don-hang/don-hang', [
            'don_hangs' => $donHangs
        ]);
    }

    public function updateTrangThai(Request $request)
    {
        $request->validate([
            'trang_thai_don_hang' => 'required|in:Đang xử lý,Vận chuyển,Đã nhận,Hủy',
        ]);

        $id_don_hang = $request->id_don_hang;
        $trang_thai_don_hang = $request->input('trang_thai_don_hang');
        $trang_thai_thanh_toan = $request->input('trang_thai_thanh_toan');

        if ($trang_thai_don_hang === 'Hủy') {
            $chiTietDonHang = DB::table('don_hang_chi_tiet')
                ->where('id_don_hang', $id_don_hang)
                ->select('id_san_pham_chi_tiet', 'so_luong')
                ->get();

            foreach ($chiTietDonHang as $item) {
                DB::table('san_pham_chi_tiet')
                    ->where('id_san_pham_chi_tiet', $item->id_san_pham_chi_tiet)
                    ->increment('so_luong_ton', $item->so_luong);
            }
        }

        DB::table('don_hang')
            ->where('id_don_hang', $request->id_don_hang)
            ->update([
                'trang_thai_thanh_toan' => $trang_thai_thanh_toan,
                'trang_thai_don_hang' => $trang_thai_don_hang,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'cập nhật thành công');
    }
}
