<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{

    public function doanhThu(Request $request)
{
    $type = $request->input('type', 'month');

    $query = DB::table('don_hang as dh')
        ->join('don_hang_chi_tiet as ct', 'dh.id_don_hang', '=', 'ct.id_don_hang')
        ->where('dh.trang_thai_thanh_toan', 'Đã thanh toán');

    switch ($type) {
        case 'day':
            $query->selectRaw("
                    TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM-DD') as thoi_gian,
                    SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu
                ")
                ->groupBy('thoi_gian')
                ->orderBy('thoi_gian');
            break;

        case 'year':
            $query->selectRaw("
                    TO_CHAR(dh.ngay_dat_hang, 'YYYY') as thoi_gian,
                    SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu
                ")
                ->groupBy('thoi_gian')
                ->orderBy('thoi_gian');
            break;

        case 'month':
        default:
            $query->selectRaw("
                    TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM') as thoi_gian,
                    SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu
                ")
                ->groupBy('thoi_gian')
                ->orderBy('thoi_gian');
            break;
    }

    return response()->json($query->get());
}



    public function tongQuan()
    {
        $tongDon = DB::table('don_hang')->count();
        $tongDoanhThu = DB::table('don_hang')
            ->where('trang_thai', 'hoan_thanh')
            ->sum('tong_tien');
        $donHomNay = DB::table('don_hang')
            ->whereDate('ngay_dat_hang', now()->toDateString())
            ->count();

        return response()->json([
            'tong_don' => $tongDon,
            'tong_doanh_thu' => $tongDoanhThu,
            'don_hom_nay' => $donHomNay,
        ]);
    }
}
