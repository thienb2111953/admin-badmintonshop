<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ThongKeSanPhamExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ThongKeController extends Controller
{
  public function doanhThu(Request $request)
  {
    $type = $request->input('type', 'month');

    $query = DB::table('don_hang as dh')
      ->join('don_hang_chi_tiet as ct', 'dh.id_don_hang', '=', 'ct.id_don_hang')
      ->join('nhap_hang_chi_tiet as nct', 'ct.id_san_pham_chi_tiet', '=', 'nct.id_san_pham_chi_tiet')
      ->where('dh.trang_thai_thanh_toan', 'Đã thanh toán');

    switch ($type) {
      case 'day':
        $query->selectRaw("
                TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM-DD') as thoi_gian,
                SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu,
                SUM(ct.so_luong * nct.don_gia) as tong_gia_von,
                SUM(ct.so_luong * ct.don_gia) - SUM(ct.so_luong * nct.don_gia) as loi_nhuan
            ")
          ->groupBy('thoi_gian')
          ->orderBy('thoi_gian');
        break;

      case 'quarter':
        $query->selectRaw("
                TO_CHAR(dh.ngay_dat_hang, 'YYYY-\"Q\"Q') as thoi_gian,
                SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu,
                SUM(ct.so_luong * nct.don_gia) as tong_gia_von,
                SUM(ct.so_luong * ct.don_gia) - SUM(ct.so_luong * nct.don_gia) as loi_nhuan
            ")
          ->groupBy('thoi_gian')
          ->orderBy('thoi_gian');
        break;

      case 'year':
        $query->selectRaw("
                TO_CHAR(dh.ngay_dat_hang, 'YYYY') as thoi_gian,
                SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu,
                SUM(ct.so_luong * nct.don_gia) as tong_gia_von,
                SUM(ct.so_luong * ct.don_gia) - SUM(ct.so_luong * nct.don_gia) as loi_nhuan
            ")
          ->groupBy('thoi_gian')
          ->orderBy('thoi_gian');
        break;

      case 'month':
      default:
        $query->selectRaw("
                TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM') as thoi_gian,
                SUM(ct.so_luong * ct.don_gia) as tong_doanh_thu,
                SUM(ct.so_luong * nct.don_gia) as tong_gia_von,
                SUM(ct.so_luong * ct.don_gia) - SUM(ct.so_luong * nct.don_gia) as loi_nhuan
            ")
          ->groupBy('thoi_gian')
          ->orderBy('thoi_gian');
        break;
    }

    return response()->json($query->get());
  }

  public function index(Request $request)
  {
    $type = $request->input('type', 'month');

    // 1. Định nghĩa định dạng thời gian dựa trên $type
    switch ($type) {
      // === THAY ĐỔI TẠI ĐÂY ===
      case 'quarter': // Đổi 'day' thành 'quarter'
        // Dùng YYYY-"Q"Q để format (ví dụ: 2024-Q1, 2024-Q2)
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY-\"Q\"Q')";
        $formatBan = "TO_CHAR(dh.ngay_dat_hang, 'YYYY-\"Q\"Q')";
        break;

      case 'year':
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY')";
        $formatBan = "TO_CHAR(dh.ngay_dat_hang, 'YYYY')";
        break;
      case 'month':
      default:
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY-MM')";
        $formatBan = "TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM')";
        break;
    }

    // 2. Subquery: Tổng hợp NHẬP HÀNG theo kỳ
    $subNhap = DB::table('nhap_hang_chi_tiet as nct')
      ->join('nhap_hang as nh', 'nct.id_nhap_hang', '=', 'nh.id_nhap_hang')
      ->select(
        'nct.id_san_pham_chi_tiet',
        DB::raw("$formatNhap as thoi_gian"),
        DB::raw('SUM(nct.so_luong) as so_luong_nhap'),
        DB::raw('AVG(nct.don_gia) as gia_nhap_tb'),
        DB::raw('0 as so_luong_ban'),
        DB::raw('0 as gia_ban_tb')
      )
      ->groupBy('thoi_gian', 'nct.id_san_pham_chi_tiet');

    // 3. Subquery: Tổng hợp BÁN HÀNG theo kỳ và UNION với Nhập Hàng
    $subBan = DB::table('don_hang_chi_tiet as dhct')
      ->join('don_hang as dh', 'dhct.id_don_hang', '=', 'dh.id_don_hang')
      ->where('dh.trang_thai_thanh_toan', 'Đã thanh toán')
      ->select(
        'dhct.id_san_pham_chi_tiet',
        DB::raw("$formatBan as thoi_gian"),
        DB::raw('0 as so_luong_nhap'),
        DB::raw('0 as gia_nhap_tb'),
        DB::raw('SUM(dhct.so_luong) as so_luong_ban'),
        DB::raw('AVG(dhct.don_gia) as gia_ban_tb')
      )
      ->groupBy('thoi_gian', 'dhct.id_san_pham_chi_tiet')
      ->unionAll($subNhap);

    // 4. Truy vấn chính: Tổng hợp từ UNION
    $query = DB::query()
      ->from(DB::raw("({$subBan->toSql()}) as t"))
      ->mergeBindings($subBan) // Quan trọng: phải merge bindings
      ->join('san_pham_chi_tiet as spc', 't.id_san_pham_chi_tiet', '=', 'spc.id_san_pham_chi_tiet')
      ->select(
        't.thoi_gian',
        't.id_san_pham_chi_tiet',
        'spc.ten_san_pham_chi_tiet',
        'spc.so_luong_ton',
        DB::raw('SUM(t.so_luong_nhap) as so_luong_nhap'),
        DB::raw('SUM(t.so_luong_ban) as so_luong_ban'),
        DB::raw('AVG(NULLIF(t.gia_nhap_tb, 0)) as gia_nhap'),
        DB::raw('AVG(NULLIF(t.gia_ban_tb, 0)) as gia_ban'),
        DB::raw('(AVG(NULLIF(t.gia_ban_tb, 0)) - AVG(NULLIF(t.gia_nhap_tb, 0))) * SUM(t.so_luong_ban) as loi_nhuan_uoc_tinh')
      )
      ->groupBy(
        't.thoi_gian',
        't.id_san_pham_chi_tiet',
        'spc.ten_san_pham_chi_tiet',
        'spc.so_luong_ton'
      )
      ->orderBy('t.thoi_gian', 'desc');

    // Lấy kết quả truy vấn
    $thong_ke_san_pham = $query->get();

    // Trả về Inertia render
    return Inertia::render('admin/thong-ke-san-pham/thong-ke-san-pham', [
      'thong_ke_san_phams' => $thong_ke_san_pham,
      'filters' => [
        'type' => $type
      ]
    ]);
  }

  public function export(Request $request)
  {
    $type = $request->input('type', 'month');

    /*
    |-----------------------------------------
    | 1. Xác định format + filter thời gian + title
    |-----------------------------------------
    */
    switch ($type) {
      case 'quarter':
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY-\"Q\"Q')";
        $formatBan  = "TO_CHAR(dh.ngay_dat_hang, 'YYYY-\"Q\"Q')";
        $thoiGianValue = $request->year . '-Q' . $request->quarter;
        $thoiGianTitle = "Thời gian thống kê: Quý {$request->quarter} / {$request->year}";
        break;

      case 'year':
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY')";
        $formatBan  = "TO_CHAR(dh.ngay_dat_hang, 'YYYY')";
        $thoiGianValue = $request->year;
        $thoiGianTitle = "Thời gian thống kê: Năm {$request->year}";
        break;

      default: // month
        $formatNhap = "TO_CHAR(nh.ngay_nhap, 'YYYY-MM')";
        $formatBan  = "TO_CHAR(dh.ngay_dat_hang, 'YYYY-MM')";
        $thoiGianValue = $request->month;
        $thoiGianTitle = "Thời gian thống kê: Tháng {$request->month}";
        break;
    }

    /*
    |-----------------------------------------
    | 2. Subquery GIÁ NHẬP ALL-TIME (KHÔNG lọc thời gian)
    |-----------------------------------------
    */
    $giaNhapAllTime = DB::table('nhap_hang_chi_tiet as nct')
      ->select(
        'nct.id_san_pham_chi_tiet',
        DB::raw('
        CASE
          WHEN SUM(nct.so_luong) = 0 THEN 0
          ELSE SUM(nct.so_luong * nct.don_gia) / SUM(nct.so_luong)
        END as gia_nhap_tb_all
      ')
      )
      ->groupBy('nct.id_san_pham_chi_tiet');

    /*
    |-----------------------------------------
    | 3. Subquery NHẬP (chỉ để gom số lượng theo kỳ)
    |-----------------------------------------
    */
    $subNhap = DB::table('nhap_hang_chi_tiet as nct')
      ->join('nhap_hang as nh', 'nct.id_nhap_hang', '=', 'nh.id_nhap_hang')
      ->select(
        'nct.id_san_pham_chi_tiet',
        DB::raw("$formatNhap as thoi_gian"),
        DB::raw('SUM(nct.so_luong) as so_luong_nhap'),
        DB::raw('0 as so_luong_ban'),
        DB::raw('0 as gia_ban_tb')
      )
      ->groupBy('thoi_gian', 'nct.id_san_pham_chi_tiet');

    /*
    |-----------------------------------------
    | 4. Subquery BÁN
    |-----------------------------------------
    */
    $subBan = DB::table('don_hang_chi_tiet as dhct')
      ->join('don_hang as dh', 'dhct.id_don_hang', '=', 'dh.id_don_hang')
      ->where('dh.trang_thai_thanh_toan', 'Đã thanh toán')
      ->select(
        'dhct.id_san_pham_chi_tiet',
        DB::raw("$formatBan as thoi_gian"),
        DB::raw('0 as so_luong_nhap'),
        DB::raw('SUM(dhct.so_luong) as so_luong_ban'),
        DB::raw('AVG(dhct.don_gia) as gia_ban_tb')
      )
      ->groupBy('thoi_gian', 'dhct.id_san_pham_chi_tiet')
      ->unionAll($subNhap);

    /*
    |-----------------------------------------
    | 5. Query chính
    |-----------------------------------------
    */
    $query = DB::query()
      ->from(DB::raw("({$subBan->toSql()}) as t"))
      ->mergeBindings($subBan)

      ->join('san_pham_chi_tiet as spc', 't.id_san_pham_chi_tiet', '=', 'spc.id_san_pham_chi_tiet')

      // 🔥 JOIN GIÁ NHẬP ALL TIME
      ->leftJoinSub($giaNhapAllTime, 'gn', function ($join) {
        $join->on('gn.id_san_pham_chi_tiet', '=', 't.id_san_pham_chi_tiet');
      })

      ->select(
        'spc.ten_san_pham_chi_tiet',
        'spc.so_luong_ton',

        DB::raw('SUM(t.so_luong_ban) as so_luong_ban'),

        // ✅ Giá nhập TB toàn bộ lịch sử
        DB::raw('COALESCE(gn.gia_nhap_tb_all, 0) as gia_nhap_tb'),

        // Giá bán TB theo kỳ
        DB::raw('
        CASE
          WHEN SUM(t.so_luong_ban) = 0 THEN 0
          ELSE SUM(t.so_luong_ban * t.gia_ban_tb) / SUM(t.so_luong_ban)
        END as gia_ban_tb
      '),

        // Doanh thu
        DB::raw('SUM(t.so_luong_ban * t.gia_ban_tb) as doanh_thu'),

        // ✅ Lợi nhuận chuẩn
        DB::raw('
        SUM(t.so_luong_ban * t.gia_ban_tb)
        - (COALESCE(gn.gia_nhap_tb_all, 0) * SUM(t.so_luong_ban))
        as loi_nhuan
      ')
      )
      ->where('t.thoi_gian', $thoiGianValue)
      ->groupBy(
        'spc.ten_san_pham_chi_tiet',
        'spc.so_luong_ton',
        'gn.gia_nhap_tb_all'
      )
      ->orderByDesc('so_luong_ban');

    $data = $query->get();

    /*
    |-----------------------------------------
    | 6. Export Excel
    |-----------------------------------------
    */
    $fileName = match ($type) {
      'month'   => "THONG_KE_SAN_PHAM_THANG_{$request->month}",
      'quarter' => "THONG_KE_SAN_PHAM_QUY_{$request->quarter}_{$request->year}",
      'year'    => "THONG_KE_SAN_PHAM_NAM_{$request->year}",
      default   => "THONG_KE_SAN_PHAM",
    };

    return Excel::download(
      new ThongKeSanPhamExport($data, $thoiGianTitle),
      $fileName . '.xlsx'
    );
  }

}
