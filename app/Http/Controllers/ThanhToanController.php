<?php

namespace App\Http\Controllers;

use App\Models\ThanhToan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ThanhToanController extends Controller
{
  public function index()
  {
    $thanhToans = DB::table('thanh_toan')
      ->join('don_hang', 'thanh_toan.id_don_hang', '=', 'don_hang.id_don_hang')
      ->select(
        'thanh_toan.id_thanh_toan',
        'thanh_toan.id_don_hang',
        'thanh_toan.so_tien',
        'thanh_toan.ten_ngan_hang',
        'thanh_toan.ngay_thanh_toan',
        'don_hang.ma_don_hang',
      )
      ->orderByDesc('thanh_toan.id_thanh_toan')
      ->get();

    return Inertia::render('admin/thanh-toan/thanh-toan', [
      'thanh_toans' => $thanhToans,
    ]);
  }
}
