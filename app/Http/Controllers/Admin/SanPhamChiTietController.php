<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\DanhMucThuongHieu;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SanPhamChiTietController extends Controller
{
    public function index($id_san_pham)
    {
        $sanPham = SanPham::with('danhMucThuongHieu')->find($id_san_pham);

        $anh_san_pham = SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get()
            ->groupBy('ten_mau')
            ->map(function ($items) {
                return [
                    'id_san_pham_chi_tiet' => $items->first()->id_san_pham_chi_tiet,
                    'ten_mau' => $items->first()->ten_mau,
                    // 'ten_kich_thuoc' => $items->pluck('ten_kich_thuoc')->implode(', '), // chuỗi
                    // 'ten_kich_thuoc' => $items->pluck('ten_kich_thuoc')->toArray(), // mảng
                    'files_anh_san_pham' => $items->flatMap(function ($item) {
                        return $item->anhSanPham->pluck('anh_url');
                    })->values()->toArray(),
                ];
            })
            ->values();

        $san_pham_chi_tiet = SanPhamChiTiet::where('id_san_pham', $id_san_pham)->get();

        return Inertia::render('admin/san-pham-chi-tiet/san-pham-chi-tiet', [
            'san_pham_info' => $sanPham,
            'anh_san_phams' => $anh_san_pham,
            'san_pham_chi_tiets' => $san_pham_chi_tiet,
        ]);
    }

    public function store(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'ten_kich_thuoc'       => 'nullable|string',
            'so_luong_ton'         => 'required|integer',
        ], [
            'so_luong_ton.required' => 'Không để trống số lượng tồn',
        ]);

        SanPhamChiTiet::create([
            'id_san_pham'   => $id_san_pham,
            'ten_mau'       => $validated['ten_mau'] ?? null,
            'ten_kich_thuoc' => $validated['ten_kich_thuoc'] ?? null,
            'so_luong_ton' => $validated['so_luong_ton'],
        ]);

        return redirect()->back()->with('success', 'Thêm sản phẩm chi tiết thành công');
    }


    public function update(Request $request, $id_san_pham)
    {
        $validated = $request->validate([
            'ten_mau'              => 'nullable|string',
            'ten_kich_thuoc'       => 'nullable|string',
            'so_luong_ton'         => 'required|integer',
        ], [
            'so_luong_ton.required' => 'Không để trống số lượng tồn',
        ]);

        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham_chi_tiet->update($validated);

        return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
    }


    public function destroy(Request $request, $id_san_pham)
    {
        $san_pham_chi_tiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

        $san_pham_chi_tiet->delete();

        return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    }

    // public function store(Request $request, $id_san_pham)
    // {
    //     $validated = $request->validate([
    //         // 'ten_kich_thuoc'       => 'nullable|string',
    //         'ten_mau'              => [
    //             'nullable',
    //             'string',
    //             Rule::unique('san_pham_chi_tiet')
    //                 ->where(fn($query) => $query->where('id_san_pham', $id_san_pham)),
    //         ],
    //         'files_anh_san_pham'   => 'nullable|array',
    //         'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png|max:2048',
    //     ], [
    //         'files_anh_san_pham.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
    //         'files_anh_san_pham.*.max'   => 'Kích thước ảnh tối đa 2MB.',
    //         'ten_mau.unique'             => 'Màu này đã tồn tại cho sản phẩm!',
    //     ]);

    //     // Lấy danh sách kích thước (mảng)
    //     // $ten_kich_thuocs = array_filter(array_map('trim', explode(',', $validated['ten_kich_thuoc'] ?? '')));

    //     // Nếu không có kích thước thì vẫn tạo 1 bản ghi
    //     // if (empty($ten_kich_thuocs)) {
    //     //     $ten_kich_thuocs = [null];
    //     // }

    //     // $id_spct = '';
    //     // foreach ($ten_kich_thuocs as $ten_kich_thuoc) {
    //     //     $san_pham_chi_tiet = SanPhamChiTiet::create([
    //     //         'id_san_pham'   => $id_san_pham,
    //     //         'ten_kich_thuoc' => $ten_kich_thuoc,
    //     //         'ten_mau'       => $validated['ten_mau'] ?? null,
    //     //     ]);

    //     //     $id_spct = $san_pham_chi_tiet->id_san_pham_chi_tiet;
    //     // }

    //     $san_pham = SanPham::find($id_san_pham);

    //     $san_pham_chi_tiet = SanPhamChiTiet::create([
    //         'id_san_pham'   => $id_san_pham,
    //         'ten_mau'       => $validated['ten_mau'] ?? null,
    //     ]);

    //     // Upload ảnh và gán cho tất cả id_san_pham_chi_tiet đã tạo
    //     if ($request->hasFile('files_anh_san_pham')) {
    //         foreach ($request->file('files_anh_san_pham') as $file) {
    //             $ten  = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
    //             $time = now()->format('Ymd_His');
    //             $unique = uniqid();
    //             $ext  = $file->getClientOriginalExtension();
    //             $filename = "{$ten}_{$time}_{$unique}.{$ext}";

    //             $path = $file->storeAs('anh_san_phams', $filename, 'public');

    //             AnhSanPham::create([
    //                 'id_san_pham_chi_tiet' => $san_pham_chi_tiet->id_san_pham_chi_tiet,
    //                 'anh_url'     => $path,
    //             ]);
    //         }
    //     }
    //     return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
    // }


    // public function update(Request $request, $id_san_pham)
    // {
    //     $sanPhamChiTiet = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

    //     $validated = $request->validate([
    //         // 'ten_kich_thuoc' => 'nullable|string',
    //         'ten_mau' => [
    //             'nullable',
    //             'string',
    //             Rule::unique('san_pham_chi_tiet')
    //                 ->where(fn($q) => $q->where('id_san_pham', $sanPhamChiTiet->id_san_pham))
    //                 ->ignore($sanPhamChiTiet->id_san_pham_chi_tiet, 'id_san_pham_chi_tiet'),
    //         ],
    //         'files_anh_san_pham'   => 'nullable|array',
    //         'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png|max:2048',
    //     ], [
    //         'ten_mau.unique'             => 'Màu này đã tồn tại cho sản phẩm!',
    //         'files_anh_san_pham.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
    //         'files_anh_san_pham.*.max'   => 'Kích thước ảnh tối đa 2MB.',
    //     ]);

    //     $sanPhamChiTiet->update([
    //         'ten_mau'        => $validated['ten_mau'] ?? $sanPhamChiTiet->ten_mau,
    //     ]);

    //     // Tách danh sách kích thước
    //     // $ten_kich_thuocs = array_filter(array_map('trim', explode(',', $validated['ten_kich_thuoc'] ?? '')));
    //     // if (empty($ten_kich_thuocs)) {
    //     //     $ten_kich_thuocs = [null];
    //     // }

    //     // // Lấy tất cả chi tiết sản phẩm theo màu cần update
    //     // $chiTiets = SanPhamChiTiet::where('id_san_pham', $id_san_pham)
    //     //     ->where('ten_mau', $validated['ten_mau'] ?? null)
    //     //     ->get();

    //     // Tạo lại chi tiết sản phẩm với kích thước mới
    //     // $id_spct = [];
    //     // foreach ($ten_kich_thuocs as $ten_kich_thuoc) {
    //     //     $san_pham_chi_tiet = SanPhamChiTiet::create([
    //     //         'id_san_pham'   => $id_san_pham,
    //     //         'ten_kich_thuoc' => $ten_kich_thuoc,
    //     //         'ten_mau'       => $validated['ten_mau'] ?? null,
    //     //     ]);

    //     //     $id_spct[] = $san_pham_chi_tiet->id_san_pham_chi_tiet;
    //     // }

    //     // Upload lại ảnh mới
    //     if ($request->hasFile('files_anh_san_pham')) {
    //         // Xoá ảnh cũ (DB + Storage)
    //         $ds_anh_cu = AnhSanPham::where('id_san_pham_chi_tiet', $request->id_san_pham_chi_tiet)->get();

    //         foreach ($ds_anh_cu as $anh) {
    //             Storage::disk('public')->delete($anh->anh_url);
    //             $anh->delete();
    //         }

    //         $san_pham = SanPham::find($id_san_pham);

    //         foreach ($request->file('files_anh_san_pham') as $file) {
    //             $ten  = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
    //             $time = now()->format('Ymd_His');
    //             $unique = uniqid();
    //             $ext  = $file->getClientOriginalExtension();
    //             $filename = "{$ten}_{$time}_{$unique}.{$ext}";

    //             $path = $file->storeAs('anh_san_phams', $filename, 'public');

    //             AnhSanPham::create([
    //                 'id_san_pham_chi_tiet' => $request->id_san_pham_chi_tiet,
    //                 'anh_url'     => $path,
    //             ]);
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Cập nhật chi tiết sản phẩm thành công');
    // }


    // public function destroy(Request $request, $id_san_pham)
    // {
    //     $san_pham = SanPhamChiTiet::findOrFail($request->id_san_pham_chi_tiet);

    //     $ds_anh_cu = AnhSanPham::where('id_san_pham_chi_tiet', $request->id_san_pham_chi_tiet)->get();

    //     if ($ds_anh_cu) {
    //         foreach ($ds_anh_cu as $anh) {
    //             Storage::disk('public')->delete($anh->anh_url);
    //         }
    //     }

    //     $san_pham->delete();

    //     return redirect()->back()->with('success', 'Xóa chi tiết thành công');
    // }
}
