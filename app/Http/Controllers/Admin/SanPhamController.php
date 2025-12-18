<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMucThuongHieu;
use App\Models\SanPham;
use App\Models\SanPhamThuocTinh;
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
      $danhMucThuongHieu = DanhMucThuongHieu::with([
          'danhMuc.thuocTinhs.chiTiets'
      ])->findOrFail($id_danh_muc_thuong_hieu); // Nên dùng findOrFail

      // 1. Lấy danh sách Thuộc Tính (ds_thuoc_tinh) từ Danh Mục (danhMuc)
      $ds_thuoc_tinh = $danhMucThuongHieu->danhMuc->thuocTinhs ?? collect();

      return Inertia::render('admin/san-pham/edit-page', [
      'id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu,
        'ds_thuoc_tinh' => $ds_thuoc_tinh,
    ]);
  }

  public function store(Request $request, $id_danh_muc_thuong_hieu)
  {
    $validated = $request->validate(
      [
        'ma_san_pham'  => 'required|string|max:255|unique:san_pham,ma_san_pham',
        'ten_san_pham' => 'required|string|max:255',
        'slug'         => 'required|string|max:255|unique:san_pham,slug',
        'mo_ta'        => 'nullable|string',
        'trang_thai'   => 'required|string|in:Đang sản xuất,Hết sản xuất',
      ],
      [
        'ma_san_pham.required' => 'Mã sản phẩm không được để trống.',
        'ma_san_pham.unique'   => 'Mã sản phẩm đã tồn tại.',

        'ten_san_pham.required' => 'Tên sản phẩm không được để trống.',

        'slug.required' => 'Slug không được để trống.',
        'slug.unique'   => 'Slug đã tồn tại.',

        'trang_thai.required' => 'Vui lòng chọn trạng thái.',
        'trang_thai.in'       => 'Trạng thái không hợp lệ.',
      ]
    );

    $validated['id_danh_muc_thuong_hieu'] = $id_danh_muc_thuong_hieu;

      // 2️⃣ Tạo sản phẩm
      $sanPham = SanPham::create($validated);

      // 3️⃣ Lặp qua các field trong request để tìm những field có prefix "thuoc_tinh_"
      $thuocTinhs = collect($request->all())
          ->filter(fn($value, $key) => str_starts_with($key, 'thuoc_tinh_') && !empty($value));

      // 4️⃣ Lưu vào bảng trung gian san_pham_thuoc_tinh
      foreach ($thuocTinhs as $key => $idThuocTinhChiTiet) {
          SanPhamThuocTinh::create([
              'id_san_pham' => $sanPham->id_san_pham,
              'id_thuoc_tinh_chi_tiet' => $idThuocTinhChiTiet,
          ]);
      }

     return redirect()
      ->route('san_pham', ['id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu])
      ->with('success', 'Cập nhật thành công');
  }

  public function updateView($id_danh_muc_thuong_hieu, $id_san_pham)
  {
      $san_pham = SanPham::with([
          'thuocTinhs.thuocTinh', // lấy thuộc tính đã gắn
          'danhMucThuongHieu.danhMuc.thuocTinhs.chiTiets' // lấy danh sách thuộc tính cần chọn
      ])
          ->where('id_danh_muc_thuong_hieu', $id_danh_muc_thuong_hieu)
          ->where('id_san_pham', $id_san_pham)
          ->firstOrFail();

      // Tạo biến riêng cho danh sách thuộc tính của danh mục
      $ds_thuoc_tinh = $san_pham->danhMucThuongHieu->danhMuc->thuocTinhs ?? collect();

      return Inertia::render('admin/san-pham/edit-page', [
          'id_danh_muc_thuong_hieu' => $id_danh_muc_thuong_hieu,
          'san_pham' => $san_pham,            // thông tin sản phẩm
          'ds_thuoc_tinh' => $ds_thuoc_tinh,  // danh sách thuộc tính để render combobox
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

      // 3️⃣ Lấy danh sách thuộc tính chi tiết từ request
      $thuocTinhs = collect($request->all())
          ->filter(fn($value, $key) => str_starts_with($key, 'thuoc_tinh_') && !empty($value));

      // 4️⃣ Xóa thuộc tính cũ và thêm lại (đồng bộ)
      SanPhamThuocTinh::where('id_san_pham', $san_pham->id_san_pham)->delete();

      $records = $thuocTinhs->map(fn($idChiTiet) => [
          'id_san_pham' => $san_pham->id_san_pham,
          'id_thuoc_tinh_chi_tiet' => $idChiTiet,
      ])->toArray();

      if (!empty($records)) {
          SanPhamThuocTinh::insert($records);
      }

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
