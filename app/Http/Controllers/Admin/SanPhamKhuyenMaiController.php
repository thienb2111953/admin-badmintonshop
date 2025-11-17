<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPhamKhuyenMai;
use Illuminate\Http\Request;

class SanPhamKhuyenMaiController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_san_pham'   => 'required|exists:san_pham,id_san_pham',
            'id_khuyen_mai' => 'required|exists:khuyen_mai,id_khuyen_mai',
        ], [
            'id_san_pham.required'   => 'Vui lòng chọn sản phẩm',
            'id_san_pham.exists'     => 'Sản phẩm không tồn tại',
            'id_khuyen_mai.required' => 'Vui lòng chọn chương trình khuyến mãi',
            'id_khuyen_mai.exists'   => 'Khuyến mãi không tồn tại',
        ]);


        SanPhamKhuyenMai::create($validatedData);

        return redirect()->back()->with('success', 'Thêm khuyến mãi thành công');
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id_san_pham'   => 'required|exists:san_pham,id_san_pham',
            'id_khuyen_mai' => 'required|exists:khuyen_mai,id_khuyen_mai',
        ], [
            'id_san_pham.required'   => 'Vui lòng chọn sản phẩm',
            'id_san_pham.exists'     => 'Sản phẩm không tồn tại',
            'id_khuyen_mai.required' => 'Vui lòng chọn chương trình khuyến mãi',
            'id_khuyen_mai.exists'   => 'Khuyến mãi không tồn tại',
        ]);

        $item = SanPhamKhuyenMai::findOrFail($request->id_san_pham_khuyen_mai);

        $item->update($validatedData);

        return redirect()->back()->with('success', 'Cập nhật thành công');
    }


    public function destroy(Request $request)
    {
        $item = SanPhamKhuyenMai::findOrFail($request->id_san_pham_khuyen_mai);

        $item->delete();

        return redirect()->back()->with('success', 'Xóa thành công');
    }
}
