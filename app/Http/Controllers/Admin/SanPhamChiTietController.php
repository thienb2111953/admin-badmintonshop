<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\DanhMucThuongHieu;
use App\Models\KichThuoc;
use App\Models\Mau;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SanPhamChiTietController extends Controller
{
  public function index($id_san_pham)
  {
    $sanPham = SanPham::with('danhMucThuongHieu')->find($id_san_pham);

      $ds_mau = DB::table('san_pham_chi_tiet as spct')
          ->leftJoin('mau as m', 'm.id_mau', '=', 'spct.id_mau')
          ->where('spct.id_san_pham', $id_san_pham)
          ->select(
              'spct.id_mau',
              'm.ten_mau',
              DB::raw('MIN(spct.id_san_pham_chi_tiet) as id_san_pham_chi_tiet_dai_dien')
          )
          ->groupBy('spct.id_mau', 'm.ten_mau')
          ->get();

      $anh_theo_mau = $ds_mau->map(function ($mau) {

          // Lấy ảnh theo id_san_pham_chi_tiet đại diện
          $anh = DB::table('anh_san_pham')
              ->where('id_san_pham_chi_tiet', $mau->id_san_pham_chi_tiet_dai_dien)
              ->orderBy('thu_tu')
              ->get()
              ->map(function ($x) {
                  return [
                      'id_anh_san_pham'      => $x->id_anh_san_pham,
                      'anh_url'              => $x->anh_url,
                      'thu_tu'               => $x->thu_tu,
                      'id_san_pham_chi_tiet' => $x->id_san_pham_chi_tiet,
                  ];
              })
              ->values();

          return [
              'id_mau'                 => $mau->id_mau,
              'ten_mau'                => $mau->ten_mau,
              'id_san_pham_chi_tiet'   => $mau->id_san_pham_chi_tiet_dai_dien,

              // ⭐ FE dùng cái này để update ảnh — GIỮ FORMAT Y CHANG bạn đang dùng
              'path_anh_san_pham_old'  => $anh,
          ];
      });

      $san_pham_chi_tiet = SanPhamChiTiet::with(['mau', 'kichThuoc'])
      ->where('id_san_pham', $id_san_pham)
        ->orderBy('id_mau', 'asc')
      ->get();

    return Inertia::render('admin/san-pham-chi-tiet/san-pham-chi-tiet', [
      'maus' => Mau::all(),
      'kich_thuocs' => KichThuoc::all(),
      'san_pham_info' => $sanPham,
      'anh_san_phams' => $anh_theo_mau,
      'san_pham_chi_tiets' => $san_pham_chi_tiet,
    ]);
  }

  public function store(Request $request, $id_san_pham)
  {
    $validated = $request->validate(
      [
          'id_mau' => [
              'required',
              'integer',
              Rule::unique('san_pham_chi_tiet')->where(function ($query) use ($request, $id_san_pham) {
                  return $query->where('id_kich_thuoc', $request->input('id_kich_thuoc'))
                      ->where('id_san_pham', $id_san_pham);
              }),
          ],
        'id_kich_thuoc' => 'required|integer',
          'gia_niem_yet' => 'nullable|integer|min:0|max:999999999999',
          'gia_ban' => 'nullable|integer|min:0|max:999999999999',
      ],
      [
        'id_kich_thuoc.required' => 'Không để trống kích thước',
        'id_mau.required' => 'Không để trống màu',
          'id_mau.unique' => 'Sự kết hợp của Màu sắc và Kích thước này đã tồn tại cho sản phẩm.',
          'gia_niem_yet.min' => 'Giá niêm yết không nhỏ hơn 0',
          'gia_ban.min' => 'Giá bán không nhỏ hơn 0',
      ],
    );

    $tenSanPham = SanPham::where('id_san_pham', $id_san_pham)->value('ten_san_pham');
    $tenMau = Mau::where('id_mau', $validated['id_mau'])->value('ten_mau');
    $tenKichThuoc = KichThuoc::where('id_kich_thuoc', $validated['id_kich_thuoc'])->value('ten_kich_thuoc');

    $tenChiTiet = trim("{$tenSanPham} - {$tenMau} - {$tenKichThuoc}", ' -');

    $chiTiet = SanPhamChiTiet::create([
      'id_san_pham' => $id_san_pham,
      'id_mau' => $validated['id_mau'],
      'id_kich_thuoc' => $validated['id_kich_thuoc'],
      'ten_san_pham_chi_tiet' => $tenChiTiet,
        'gia_niem_yet' => $validated['gia_niem_yet'],
        'gia_ban' => $validated['gia_ban'],
    ]);

    return redirect()->back()->with('success', 'Thêm sản phẩm chi tiết thành công');
  }

  public function update(Request $request, $id_san_pham)
  {
    $validated = $request->validate(
      [
        'id_san_pham_chi_tiet' => 'required|integer',
        'id_mau' => 'required|integer',
        'id_kich_thuoc' => 'required|integer',
          'gia_niem_yet' => 'nullable|integer|min:0|max:999999999999',
          'gia_ban' => 'nullable|integer|min:0|max:999999999999',
      ],
      [
        'id_san_pham_chi_tiet.required' => 'Không tìm thấy sản phẩm chi tiết',
        'id_kich_thuoc.required' => 'Không để trống kích thước',
        'id_mau.required' => 'Không để trống màu',
          'gia_niem_yet.min' => 'Giá niêm yết không nhỏ hơn 0',
          'gia_ban.min' => 'Giá bán không nhỏ hơn 0',
      ],
    );

    $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($validated['id_san_pham_chi_tiet']);

    $tenSanPham = SanPham::where('id_san_pham', $id_san_pham)->value('ten_san_pham');
    $tenMau = Mau::where('id_mau', $validated['id_mau'])->value('ten_mau');
    $tenKichThuoc = KichThuoc::where('id_kich_thuoc', $validated['id_kich_thuoc'])->value('ten_kich_thuoc');

    $tenChiTiet = trim("{$tenSanPham} - {$tenMau} - {$tenKichThuoc}", ' -');

    $san_pham_chi_tiet->update([
      'id_san_pham' => $id_san_pham,
      'id_mau' => $validated['id_mau'],
      'id_kich_thuoc' => $validated['id_kich_thuoc'],
      'ten_san_pham_chi_tiet' => $tenChiTiet,
        'gia_niem_yet' => $validated['gia_niem_yet'],
        'gia_ban' => $validated['gia_ban'],
    ]);

    return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
  }

  public function destroy(Request $request, $id_san_pham)
  {
    $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

    $san_pham_chi_tiet->delete();

    return redirect()->back()->with('success', 'Xóa chi tiết thành công');
  }
}
