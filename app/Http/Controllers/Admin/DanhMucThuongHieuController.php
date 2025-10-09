<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\DanhMucThuocTinh;
use App\Models\DanhMucThuongHieu;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DanhMucThuongHieuController extends Controller
{
  public function index()
  {
    // $danh_muc_thuong_hieus = DanhMucThuongHieu::where([
    //     ['id_thuong_hieu', $id_thuong_hieu],
    // ])
    //     ->orderBy('id_danh_muc_thuong_hieu', 'asc')
    //     ->get();

    $danh_mucs = DanhMuc::all();
    $thuong_hieus = ThuongHieu::all();
    $danh_muc_thuong_hieus = DanhMucThuongHieu::all();

    return Inertia::render('admin/danh-muc-thuong-hieu/danh-muc-thuong-hieu', [
      // 'thuong_hieu_info' => ThuongHieu::find($id_thuong_hieu),
      'danh_muc_thuong_hieus' => $danh_muc_thuong_hieus,
      'danh_mucs' => $danh_mucs,
      'thuong_hieus' => $thuong_hieus,
    ]);
  }

  public function storeView()
  {
    $danh_mucs = DanhMuc::all();
    $thuong_hieus = ThuongHieu::all();

    return Inertia::render('admin/danh-muc-thuong-hieu/them-moi', [
      'danh_mucs' => $danh_mucs,
      'thuong_hieus' => $thuong_hieus,
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'ten_danh_muc_thuong_hieu' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'mo_ta' => 'nullable|string',
        'id_danh_muc' => 'required|integer',
        'id_thuong_hieu' => 'required|integer',
      ],
      [
        'ten_danh_muc_thuong_hieu.max' => 'Vượt quá số ký tự quy định (255 ký tự)',
        'ten_danh_muc_thuong_hieu.required' => 'Tên danh mục thương hiệu là bắt buộc',
        'slug.required' => 'Slug không được để trống',
        'id_danh_muc.required' => 'Vui lòng chọn danh mục',
        'id_thuong_hieu.required' => 'Vui lòng chọn thương hiệu',
      ],
    );

    DanhMucThuongHieu::create($validated);

    return redirect()->back()->with('success', 'Tạo thành công');
  }

  public function update(Request $request)
  {
    $validated = $request->validate(
      [
        'ten_danh_muc_thuong_hieu' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'mo_ta' => 'nullable|string',
        'id_danh_muc' => 'required|integer',
        'id_thuong_hieu' => 'required|integer',
      ],
      [
        'ten_danh_muc_thuong_hieu.max' => 'Vượt quá số ký tự quy định (255 ký tự)',
        'ten_danh_muc_thuong_hieu.required' => 'Tên danh mục thương hiệu là bắt buộc',
        'slug.required' => 'Slug không được để trống',
        'id_danh_muc.required' => 'Vui lòng chọn danh mục',
        'id_thuong_hieu.required' => 'Vui lòng chọn thương hiệu',
      ],
    );

    $danh_muc_thuoc_tinh = DanhMucThuongHieu::findOrFail($request->id_danh_muc_thuong_hieu);

    $danh_muc_thuoc_tinh->update($validated);

    return redirect()->back()->with('success', 'Cập nhật thành công');
  }

  public function destroy(Request $request)
  {
    $danh_muc_thuoc_tinh = DanhMucThuongHieu::findOrFail($request->id_danh_muc_thuong_hieu);

    $danh_muc_thuoc_tinh->delete();

    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
