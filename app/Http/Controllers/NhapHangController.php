<?php

namespace App\Http\Controllers;

use App\Models\NhapHang;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NhapHangController extends Controller
{
  public function index()
  {
    return Inertia::render('admin/nhap_hang/nhap_hang', [
      'nhap_hangs' => NhapHang::all(),
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'ma_nhap_hang' => 'required|string|max:255',
      ],
      [
        'ma_nhap_hang.required' => 'Mã nhập hàng không được để trống',
      ],
    );

    NhapHang::create($validated);
    return redirect()->back()->with('success', 'Tạo thành công');
  }

  public function update(Request $request)
  {
    $validated = $request->validate(
      [
        'ma_nhap_hang' => 'required|string|max:255',
      ],
      [
        'ma_nhap_hang.required' => 'Mã nhập hàng không được để trống',
      ],
    );

    $nhap_hang = NhapHang::findOrFail($request->id_nhap_hang);

    $nhap_hang->update($validated);
    return redirect()->back()->with('success', 'cập nhật thành công');
  }

  public function destroy(Request $request)
  {
    $nhap_hang = NhapHang::findOrFail($request->id_nhap_hang);

    $nhap_hang->delete();
    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
