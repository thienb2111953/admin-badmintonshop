<?php

namespace App\Http\Controllers;

use App\Models\NhapHang;
use App\Models\NhapHangChiTiet;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NhapHangChiTietController extends Controller
{
  public function index($id_nhap_hang)
  {
    $nhapHang = NhapHang::find($id_nhap_hang);

    $chiTiet = NhapHangChiTiet::query()
      ->with(['nhapHang', 'sanPhamChiTiet'])
      ->where('id_nhap_hang', $id_nhap_hang)
      ->get()
      ->map(function ($item) {
        return [
          'id_nhap_hang_chi_tiet' => $item->id_nhap_hang_chi_tiet,
          'id_nhap_hang' => $item->id_nhap_hang,
          'id_san_pham_chi_tiet' => $item->id_san_pham_chi_tiet,
          'so_luong' => $item->so_luong,
          'don_gia' => $item->don_gia,
          'ten_san_pham_chi_tiet' => $item->sanPhamChiTiet->ten_san_pham_chi_tiet ?? '',
        ];
      });

    return Inertia::render('admin/nhap-hang-chi-tiet/nhap-hang-chi-tiet', [
      'nhap_hang_info' => $nhapHang,
      'nhap_hang_chi_tiets' => $chiTiet,
      'san_pham_chi_tiets' => SanPhamChiTiet::all(),
    ]);
  }

  public function store(Request $request, $id_nhap_hang)
  {
    $validated = $request->validate(
      [
        'id_san_pham_chi_tiet' => 'required|exists:san_pham_chi_tiet,id_san_pham_chi_tiet',
        'so_luong' => 'required|integer|min:1',
        'don_gia' => 'required|numeric|min:0',
      ],
      [
        'id_san_pham_chi_tiet.required' => 'Vui lòng chọn sản phẩm',
        'id_san_pham_chi_tiet.exists' => 'Sản phẩm không tồn tại',
        'so_luong.required' => 'Vui lòng nhập số lượng',
        'so_luong.integer' => 'Số lượng phải là số nguyên',
        'so_luong.min' => 'Số lượng phải lớn hơn 0',
        'don_gia.required' => 'Vui lòng nhập đơn giá',
        'don_gia.numeric' => 'Đơn giá phải là số',
        'don_gia.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
      ],
    );

    $isExists = NhapHangChiTiet::where('id_nhap_hang', $id_nhap_hang)
      ->where('id_san_pham_chi_tiet', $validated['id_san_pham_chi_tiet'])
      ->exists();

    if ($isExists) {
      return redirect()
        ->back()
        ->withErrors([
          'id_san_pham_chi_tiet' => 'Sản phẩm này đã tồn tại trong phiếu nhập.',
        ]);
    }

    NhapHangChiTiet::create([
      'id_nhap_hang' => $id_nhap_hang,
      'id_san_pham_chi_tiet' => $validated['id_san_pham_chi_tiet'],
      'so_luong' => $validated['so_luong'],
      'don_gia' => $validated['don_gia'],
    ]);

    $sanPhamChiTiet = SanPhamChiTiet::find($validated['id_san_pham_chi_tiet']);
    if ($sanPhamChiTiet) {
      $sanPhamChiTiet->increment('so_luong_ton', $validated['so_luong']);
    }

    return redirect()->back()->with('success', 'Thêm sản phẩm vào phiếu nhập thành công');
  }

  public function update(Request $request, $id_nhap_hang)
  {
    $validated = $request->validate([
      'id_nhap_hang_chi_tiet' => 'required|exists:nhap_hang_chi_tiet,id_nhap_hang_chi_tiet',
      'so_luong' => 'required|integer|min:1',
      'don_gia' => 'required|numeric|min:0',
    ]);

    $chiTiet = NhapHangChiTiet::findOrFail($validated['id_nhap_hang_chi_tiet']);

    // 🧮 Tính chênh lệch số lượng
    $soLuongCu = $chiTiet->so_luong;
    $soLuongMoi = $validated['so_luong'];
    $chenhLech = $soLuongMoi - $soLuongCu;

    // ✅ Cập nhật chi tiết nhập hàng
    $chiTiet->update([
      'so_luong' => $soLuongMoi,
      'don_gia' => $validated['don_gia'],
    ]);

    // ✅ Cập nhật tồn kho
    if ($chenhLech !== 0) {
      $sanPhamChiTiet = SanPhamChiTiet::find($chiTiet->id_san_pham_chi_tiet);
      if ($sanPhamChiTiet) {
        // nếu chênh lệch dương -> cộng, âm -> trừ
        $sanPhamChiTiet->increment('so_luong_ton', $chenhLech);
      }
    }

    return redirect()->back()->with('success', 'Cập nhật thành công');
  }

  public function destroy(Request $request, $id_nhap_hang)
  {
    $request->validate([
      'id_nhap_hang_chi_tiet' => 'required|exists:nhap_hang_chi_tiet,id_nhap_hang_chi_tiet',
    ]);

    $chiTiet = NhapHangChiTiet::findOrFail($request->id_nhap_hang_chi_tiet);

    // ✅ Trừ tồn kho trước khi xóa
    $sanPhamChiTiet = SanPhamChiTiet::find($chiTiet->id_san_pham_chi_tiet);
    if ($sanPhamChiTiet) {
      $sanPhamChiTiet->decrement('so_luong_ton', $chiTiet->so_luong);
    }

    // ✅ Xóa chi tiết phiếu nhập
    $chiTiet->delete();

    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
