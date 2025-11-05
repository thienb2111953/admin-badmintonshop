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
            'files_anh_san_pham'   => 'required|array',
            'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png,webp|max:20000',
        ], [
            'files_anh_san_pham.required' => 'chưa thêm thứ tự hình ảnh',
            'files_anh_san_pham.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'files_anh_san_pham.*.max'   => 'Kích thước ảnh tối đa 20MB.',
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
            'ten_mau'                     => 'nullable|string',
            'files_anh_san_pham_new'      => 'nullable|array',
            'files_anh_san_pham_new.*.file' => 'mimes:jpg,jpeg,png,webp|max:20000',
            'files_anh_san_pham_new.*.thu_tu' => 'required|integer',
            'path_anh_san_pham_old'       => 'nullable|array',
        ],
        [
            'files_anh_san_pham_new.*.thu_tu.required' => 'Chưa thêm thứ tự ảnh',
        ]);

        // lặp qua tất cả ảnh cũ gửi qua -> nếu ds ảnh cũ từ DB ko tồn tại trong ds ảnh cũ gửi qua thì xóa, nếu tồn tại thì cập nhật
        // Lấy danh sách ảnh cũ trong DB
        $ds_anh_cu = AnhSanPham::where('id_san_pham_chi_tiet', $request->id_san_pham_chi_tiet)->get();

        $ids_giu_lai = collect($request->path_anh_san_pham_old ?? [])
            ->pluck('id_anh_san_pham')
            ->filter()
            ->toArray();

        // Xử lý từng ảnh trong DB
        foreach ($ds_anh_cu as $anh) {
            if (!in_array($anh->id_anh_san_pham, $ids_giu_lai)) {
                // Nếu không còn trong request → xoá
                Storage::disk('public')->delete($anh->anh_url);
                $anh->delete();
            } else {
                // Nếu còn giữ lại → update thứ tự
                $fileData = collect($request->path_anh_san_pham_old)
                    ->firstWhere('id_anh_san_pham', $anh->id_anh_san_pham);

                $anh->update([
                    'thu_tu' => $fileData['thu_tu'] ?? null,
                ]);
            }
        }


        // 2. Upload ảnh mới
        if ($request->filled('files_anh_san_pham_new')) {
            $san_pham = SanPham::findOrFail($id_san_pham);

            foreach ($request->files_anh_san_pham_new as $item) {
                if (!isset($item['file']) || !$item['file'] instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                $file = $item['file'];
                $ten  = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
                $time = now()->format('Ymd_His');
                $unique = uniqid();
                $ext  = $file->getClientOriginalExtension();
                $filename = "{$ten}_{$time}_{$unique}.{$ext}";

                $path = $file->storeAs('anh_san_phams', $filename, 'public');

                AnhSanPham::create([
                    'id_san_pham_chi_tiet' => $request->id_san_pham_chi_tiet,
                    'anh_url'  => $path,
                    'thu_tu'   => $item['thu_tu'] ?? null,
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
