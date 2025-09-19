<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;


class SanPhamChiTietController extends Controller
{
    public function index($id_san_pham)
    {
        return Inertia::render('admin/san-pham-chi-tiet/san-pham-chi-tiet', [
            'san_pham_info' => SanPham::find($id_san_pham),
            'id_san_pham' => $id_san_pham,
            'san_pham_chi_tiets' => SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get(),
        ]);
    }

    public function store(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'id_kich_thuoc'     => 'nullable|integer',
            'id_mau'            => 'nullable|integer',
            'file_anh_san_pham' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'file_anh_san_pham.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'file_anh_san_pham.max'   => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $validated['id_san_pham'] = $id_san_pham;

        $ten_san_pham = SanPham::where('id_san_pham', $id_san_pham)->value('ten_san_pham');

        if ($request->hasFile('file_anh_san_pham')) {
            $file = $request->file('file_anh_san_pham');
            $ten  = Str::slug($ten_san_pham ?? 'anh');
            $time = now()->format('Ymd_His');
            $ext  = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";

            $path = $file->storeAs('anh_san_phams', $filename, 'public');

            // Tạo bản ghi trong bảng anh_san_pham
            $anh = AnhSanPham::create([
                'anh_san_pham_url' => $path,
            ]);

            // Thêm id_anh_san_pham vào validated
            $validated['id_anh_san_pham'] = $anh->id;
        }

        // Chỉ còn lưu các id cần thiết
        SanPhamChiTiet::create($validated);

        return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
    }


    public function update(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'id_kich_thuoc'     => 'nullable|integer',
            'id_mau'            => 'nullable|integer',
            'file_anh_san_pham' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'file_anh_san_pham.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'file_anh_san_pham.max'   => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $validated['id_san_pham'] = $id_san_pham;

        $ten_san_pham = SanPham::where('id_san_pham', $id_san_pham)->value('ten_san_pham');

        if ($request->hasFile('file_anh_san_pham')) {
            $file = $request->file('file_anh_san_pham');
            $ten  = Str::slug($ten_san_pham ?? 'anh');
            $time = now()->format('Ymd_His');
            $ext  = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";

            $path = $file->storeAs('anh_san_phams', $filename, 'public');

            // Tạo bản ghi trong bảng anh_san_pham
            $anh = AnhSanPham::create([
                'anh_san_pham_url' => $path,
            ]);

            // Thêm id_anh_san_pham vào validated
            $validated['id_anh_san_pham'] = $anh->id;
        }

        // Chỉ còn lưu các id cần thiết
        SanPhamChiTiet::create($validated);

        return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
    }

    public function destroy(Request $request, $id_san_pham)
    {
        $san_pham = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }
}
