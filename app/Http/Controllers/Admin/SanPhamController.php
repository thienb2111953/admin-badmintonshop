<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucThuongHieu;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class SanPhamController extends Controller
{
  public function index($id_danh_muc_thuong_hieu)
  {
    $info_dmth = DanhMucThuongHieu::find($id_danh_muc_thuong_hieu);
    $san_phams = SanPham::where('id_danh_muc_thuong_hieu', $id_danh_muc_thuong_hieu)->get();

    return Inertia::render('admin/san-pham/san-pham', [
      'info_dmth' => $info_dmth,
      'san_phams' => $san_phams,
    ]);
  }

  public function storeView($id_danh_muc_thuong_hieu)
  {
    return Inertia::render('admin/san-pham/edit-page', [
      'id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu,
    ]);
  }

  public function store(Request $request, $id_danh_muc_thuong_hieu)
  {
    $validated = $request->validate(
      [
        'ma_san_pham' => 'required|string|max:255',
        'ten_san_pham' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'mo_ta' => 'nullable|string',

        'trang_thai' => 'nullable|string',
      ],
      [
        'ma_san_pham.required' => 'Mã sản phẩm không được để trống',
        'ten_san_pham.required' => 'Tên sản phẩm không được để trống',
        'slug.required' => 'Slug không được để trống',

      ],
    );

    $validated['id_danh_muc_thuong_hieu'] = $id_danh_muc_thuong_hieu;

    SanPham::create($validated);

     return redirect()
      ->route('san_pham', ['id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu])
      ->with('success', 'Cập nhật thành công');
  }

  public function updateView($id_danh_muc_thuong_hieu, $id_san_pham)
  {
    $san_pham = SanPham::findOrFail($id_san_pham);

    return Inertia::render('admin/san-pham/edit-page', [
      'id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu,
      'san_pham' => $san_pham,
    ]);
  }

  public function update(Request $request, $id_danh_muc_thuong_hieu)
  {
    $validated = $request->validate(
      [
        'ma_san_pham' => 'required|string|max:255',
        'ten_san_pham' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'mo_ta' => 'nullable|string',

        'trang_thai' => 'nullable|string',
      ],
      [
        'ma_san_pham.required' => 'Mã sản phẩm không được để trống',
        'ten_san_pham.required' => 'Tên sản phẩm không được để trống',
        'slug.required' => 'Slug không được để trống',

      ],
    );

    $validated['id_danh_muc_thuong_hieu'] = $id_danh_muc_thuong_hieu;

    $san_pham = SanPham::findOrFail($request->id_san_pham);

    $san_pham->update($validated);

    return redirect()
      ->route('san_pham', ['id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu])
      ->with('success', 'Cập nhật thành công');
  }

  public function destroy(Request $request, $id_danh_muc_thuong_hieu)
  {
    $san_pham = SanPham::findOrFail($request->id_san_pham);

    $san_pham->delete();

    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
