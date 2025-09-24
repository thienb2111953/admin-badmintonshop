<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AnhSanPhamController extends Controller
{
    public function store(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'files_anh_san_pham'   => 'nullable|array',
            'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png|max:2048',
        ], [
            'files_anh_san_pham.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'files_anh_san_pham.*.max'   => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $san_pham = SanPham::find($id_san_pham);

        // Upload ảnh và gán cho tất cả id_san_pham_chi_tiet đã tạo
        if ($request->hasFile('files_anh_san_pham')) {
            foreach ($request->file('files_anh_san_pham') as $file) {
                $ten  = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
                $time = now()->format('Ymd_His');
                $unique = uniqid();
                $ext  = $file->getClientOriginalExtension();
                $filename = "{$ten}_{$time}_{$unique}.{$ext}";

                $path = $file->storeAs('anh_san_phams', $filename, 'public');

                AnhSanPham::create([
                    'id_san_pham_chi_tiet' => $request->id_san_pham_chi_tiet,
                    'anh_url'     => $path,
                ]);
            }
        }
        return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
    }


    public function update(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'files_anh_san_pham'   => 'nullable|array',
            'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png|max:2048',
        ], [
            'files_anh_san_pham.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'files_anh_san_pham.*.max'   => 'Kích thước ảnh tối đa 2MB.',
        ]);

        // Upload lại ảnh mới
        if ($request->hasFile('files_anh_san_pham')) {
            // Xoá ảnh cũ (DB + Storage)
            $ds_anh_cu = AnhSanPham::where('id_san_pham_chi_tiet', $request->id_san_pham_chi_tiet)->get();

            foreach ($ds_anh_cu as $anh) {
                Storage::disk('public')->delete($anh->anh_url);
                $anh->delete();
            }

            $san_pham = SanPham::find($id_san_pham);

            foreach ($request->file('files_anh_san_pham') as $file) {
                $ten  = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
                $time = now()->format('Ymd_His');
                $unique = uniqid();
                $ext  = $file->getClientOriginalExtension();
                $filename = "{$ten}_{$time}_{$unique}.{$ext}";

                $path = $file->storeAs('anh_san_phams', $filename, 'public');

                AnhSanPham::create([
                    'id_san_pham_chi_tiet' => $request->id_san_pham_chi_tiet,
                    'anh_url'     => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
    }

    public function destroy(Request $request, $id_san_pham)
    {
        $san_pham = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $ds_anh_cu = AnhSanPham::where('id_san_pham_chi_tiet', $request->id_san_pham_chi_tiet)->get();

        if ($ds_anh_cu) {
            foreach ($ds_anh_cu as $anh) {
                Storage::disk('public')->delete($anh->anh_url);
            }
        }

        $san_pham->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }
}
