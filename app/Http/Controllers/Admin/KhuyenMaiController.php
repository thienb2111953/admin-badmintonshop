<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHangKhuyenMai;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Models\SanPhamKhuyenMai;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class KhuyenMaiController extends Controller
{
  public function index()
  {
    $khuyen_mais = KhuyenMai::orderBy('gia_tri', 'asc')->get();
    $san_phams = SanPham::all();

    $san_pham_khuyen_mais = SanPhamKhuyenMai::query()
      ->selectRaw("
                DISTINCT ON (san_pham_khuyen_mai.id_san_pham)
                san_pham_khuyen_mai.*,
                sp.ten_san_pham,
                km.ma_khuyen_mai,
                spc.gia_ban,
                CASE
                    WHEN km.don_vi_tinh = 'percent'
                        THEN spc.gia_ban - (spc.gia_ban * km.gia_tri / 100)
                    WHEN km.don_vi_tinh = 'fixed'
                        THEN spc.gia_ban - km.gia_tri
                    ELSE spc.gia_ban
                END AS gia_sau_khuyen_mai
            ")
      ->leftJoin('san_pham as sp', 'sp.id_san_pham', '=', 'san_pham_khuyen_mai.id_san_pham')
      ->leftJoin('san_pham_chi_tiet as spc', 'spc.id_san_pham', '=', 'sp.id_san_pham')
      ->leftJoin('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'san_pham_khuyen_mai.id_khuyen_mai')
      ->whereDate('km.ngay_bat_dau', '<=', now())
      ->whereDate('km.ngay_ket_thuc', '>=', now())
      ->orderBy('san_pham_khuyen_mai.id_san_pham')
      ->orderBy('san_pham_khuyen_mai.id_san_pham_khuyen_mai', 'DESC')
      ->get();

    $don_hang_khuyen_mais = DonHangKhuyenMai::query()
      ->leftJoin('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'don_hang_khuyen_mai.id_khuyen_mai')
      ->selectRaw("
                don_hang_khuyen_mai.*,
                km.ma_khuyen_mai,
                CASE
                    WHEN km.don_vi_tinh = 'percent'
                        THEN don_hang_khuyen_mai.gia_tri_duoc_giam - (don_hang_khuyen_mai.gia_tri_duoc_giam * km.gia_tri / 100)
                    WHEN km.don_vi_tinh = 'fixed'
                        THEN don_hang_khuyen_mai.gia_tri_duoc_giam - km.gia_tri
                    ELSE don_hang_khuyen_mai.gia_tri_duoc_giam
                END AS gia_sau_khuyen_mai
            ")
      ->orderBy('gia_tri_duoc_giam', 'asc')
      ->get();

    return Inertia::render('admin/khuyen-mai/khuyen-mai-tabs', [
      'khuyen_mais' => $khuyen_mais,
      'san_pham_khuyen_mais' => $san_pham_khuyen_mais,
      'san_phams' => $san_phams,
      'don_hang_khuyen_mais' => $don_hang_khuyen_mais,
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'ma_khuyen_mai' => 'required|string|max:50|unique:khuyen_mai,ma_khuyen_mai',
        'ten_khuyen_mai' => 'required|string|max:255',

        'gia_tri' => 'required|integer|min:1',
        'don_vi_tinh' => 'required|in:percent,fixed',

        'ngay_bat_dau' => 'required|date',
        'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
      ],
      [
        'ma_khuyen_mai.required' => 'Mã khuyến mãi không được để trống.',
        'ma_khuyen_mai.unique' => 'Mã khuyến mãi đã tồn tại.',
        'ma_khuyen_mai.max' => 'Mã khuyến mãi tối đa 50 ký tự.',

        'ten_khuyen_mai.required' => 'Tên khuyến mãi không được để trống.',

        'gia_tri.required' => 'Giá trị khuyến mãi là bắt buộc.',
        'gia_tri.integer' => 'Giá trị khuyến mãi không hợp lệ.',
        'gia_tri.min' => 'Giá trị khuyến mãi phải lớn hơn 0.',

        'don_vi_tinh.required' => 'Vui lòng chọn đơn vị tính.',
        'don_vi_tinh.in' => 'Đơn vị tính không hợp lệ.',

        'ngay_bat_dau.required' => 'Vui lòng chọn ngày bắt đầu.',
        'ngay_bat_dau.date' => 'Ngày bắt đầu không hợp lệ.',

        'ngay_ket_thuc.required' => 'Vui lòng chọn ngày kết thúc.',
        'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
      ]
    );

    // =========================
    // VALIDATE LOGIC THEO ĐƠN VỊ
    // =========================
    if ($validated['don_vi_tinh'] === 'percent' && $validated['gia_tri'] > 100) {
      throw ValidationException::withMessages([
        'gia_tri' => 'Giảm theo phần trăm không được vượt quá 100%.',
      ]);
    }

    // =========================
    // CAST DATETIME (AN TOÀN)
    // =========================
    $validated['ngay_bat_dau'] = Carbon::parse($validated['ngay_bat_dau']);
    $validated['ngay_ket_thuc'] = Carbon::parse($validated['ngay_ket_thuc']);

    KhuyenMai::create($validated);

    return back()->with('success', 'Thêm khuyến mãi thành công');
  }

  public function update(Request $request)
  {
    $km = KhuyenMai::findOrFail($request->id_khuyen_mai);

    $validated = $request->validate(
      [
        'ma_khuyen_mai'  => [
          'required',
          'string',
          'max:50',
          Rule::unique('khuyen_mai', 'ma_khuyen_mai')
            ->ignore($km->id_khuyen_mai, 'id_khuyen_mai'),
        ],
        'ten_khuyen_mai' => 'required|string|max:255',

        'gia_tri'     => 'required|integer|min:1',
        'don_vi_tinh' => 'required|in:percent,fixed',

        'ngay_bat_dau'  => 'required|date',
        'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
      ],
      [
        'ma_khuyen_mai.required' => 'Mã khuyến mãi không được để trống.',
        'ma_khuyen_mai.unique'   => 'Mã khuyến mãi đã tồn tại.',

        'ten_khuyen_mai.required' => 'Tên khuyến mãi không được để trống.',

        'gia_tri.required' => 'Giá trị khuyến mãi là bắt buộc.',
        'gia_tri.min'      => 'Giá trị khuyến mãi phải lớn hơn 0.',

        'don_vi_tinh.required' => 'Vui lòng chọn đơn vị tính.',
        'don_vi_tinh.in'       => 'Đơn vị tính không hợp lệ.',

        'ngay_bat_dau.required' => 'Vui lòng chọn ngày bắt đầu.',
        'ngay_ket_thuc.after'   => 'Ngày kết thúc phải sau ngày bắt đầu.',
      ]
    );

    // =========================
    // LOGIC THEO ĐƠN VỊ
    // =========================
    if ($validated['don_vi_tinh'] === 'percent' && $validated['gia_tri'] > 100) {
      throw ValidationException::withMessages([
        'gia_tri' => 'Giảm theo phần trăm không được vượt quá 100%.',
      ]);
    }

    // =========================
    // CAST DATETIME (AN TOÀN)
    // =========================
    $validated['ngay_bat_dau']  = Carbon::parse($validated['ngay_bat_dau']);
    $validated['ngay_ket_thuc'] = Carbon::parse($validated['ngay_ket_thuc']);

    $km->update($validated);

    return back()->with('success', 'Cập nhật khuyến mãi thành công');
  }

  public function destroy(Request $r)
  {
    $km = KhuyenMai::findOrFail($r->id_khuyen_mai);
    $km->delete();

    return back()->with('success', 'Xóa thành công');
  }
}
