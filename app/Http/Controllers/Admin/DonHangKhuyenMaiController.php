<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHangKhuyenMai;
use Illuminate\Http\Request;

class DonHangKhuyenMaiController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'gia_tri_duoc_giam'   => 'required',
            'id_khuyen_mai' => 'required|exists:khuyen_mai,id_khuyen_mai',
        ], [
            'gia_tri_duoc_giam.required'   => 'Vui lòng điền giá trị được giảm cho đơn hàng',
            'id_khuyen_mai.required' => 'Vui lòng chọn chương trình khuyến mãi',
            'id_khuyen_mai.exists'   => 'Khuyến mãi không tồn tại',
        ]);

        DonHangKhuyenMai::create($validatedData);

        return redirect()->back()->with('success', 'Thêm khuyến mãi thành công');
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'gia_tri_duoc_giam'   => 'required',
            'id_khuyen_mai' => 'required|exists:khuyen_mai,id_khuyen_mai',
        ], [
            'gia_tri_duoc_giam.required'   => 'Vui lòng điền giá trị được giảm cho đơn hàng',
            'id_khuyen_mai.required' => 'Vui lòng chọn chương trình khuyến mãi',
            'id_khuyen_mai.exists'   => 'Khuyến mãi không tồn tại',
        ]);

        $item = DonHangKhuyenMai::findOrFail($request->id_don_hang_khuyen_mai);

        $item->update($validatedData);

        return redirect()->back()->with('success', 'Cập nhật thành công');
    }


    public function destroy(Request $request)
    {
        $item = DonHangKhuyenMai::findOrFail($request->id_don_hang_khuyen_mai);

        $item->delete();

        return redirect()->back()->with('success', 'Xóa thành công');
    }
}
