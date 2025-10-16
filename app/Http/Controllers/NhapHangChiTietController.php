<?php

namespace App\Http\Controllers;

use App\Models\NhapHangChiTiet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NhapHangChiTietController extends Controller
{
  public function index()
  {
    return Inertia::render('admin/nhap_hang/nhap_hang', [
      'nhap_hang_chi_tiets' => NhapHangChiTiet::with(['nhapHang', 'sanPhamChiTiet'])->get(),
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'id_nhap_hang' => 'required|exists:nhap_hang,id_nhap_hang',
      'id_san_pham_chi_tiet' => 'required|exists:san_pham_chi_tiet,id_san_pham_chi_tiet',
      'so_luong' => 'required|integer|min:1',
      'don_gia' => 'required|numeric|min:0',
    ]);

    NhapHangChiTiet::create($validated);

    return redirect()->back()->with('success', 'Thêm sản phẩm vào phiếu nhập thành công');
  }

  public function update(Request $request)
  {
    $validated = $request->validate([
      'id_nhap_hang_chi_tiet' => 'required|exists:nhap_hang_chi_tiet,id_nhap_hang_chi_tiet',
      'so_luong' => 'required|integer|min:1',
      'don_gia' => 'required|numeric|min:0',
    ]);

    $chiTiet = NhapHangChiTiet::findOrFail($validated['id_nhap_hang_chi_tiet']);
    $chiTiet->update($validated);

    return redirect()->back()->with('success', 'Cập nhật thành công');
  }

  public function destroy(Request $request)
  {
    $request->validate([
      'id_nhap_hang_chi_tiet' => 'required|exists:nhap_hang_chi_tiet,id_nhap_hang_chi_tiet',
    ]);

    $chiTiet = NhapHangChiTiet::findOrFail($request->id_nhap_hang_chi_tiet);
    $chiTiet->delete();

    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
