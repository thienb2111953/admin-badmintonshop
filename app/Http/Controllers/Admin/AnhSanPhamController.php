<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnhSanPham;
use App\Models\SanPham;
use App\Models\SanPhamChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AnhSanPhamController extends Controller
{
  public function store(Request $request, $id_san_pham)
  {
    $validated = $request->validate([
      'ten_mau' => 'nullable|string',
      'id_mau' => 'required|integer',
      'files_anh_san_pham' => 'nullable|array',
      'files_anh_san_pham.*' => 'mimes:jpg,jpeg,png,webp|max:2048',

      'path_anh_san_pham_old' => 'nullable|array',
      'path_anh_san_pham_old.*.id_anh_san_pham' => 'required|integer',
      'path_anh_san_pham_old.*.thu_tu' => 'required|integer|min:1',
    ], [
      'path_anh_san_pham_old.*.thu_tu.required' => 'Vui lòng nhập thứ tự cho ảnh.',
      'path_anh_san_pham_old.*.thu_tu.integer' => 'Thứ tự phải là số.',
      'path_anh_san_pham_old.*.thu_tu.min' => 'Thứ tự phải >= 1.',
    ]);

    $san_pham = SanPham::find($id_san_pham);

    // Upload ảnh và gán cho tất cả id_san_pham_chi_tiet đã tạo
    if ($request->hasFile('files_anh_san_pham')) {
      foreach ($request->file('files_anh_san_pham') as $file) {
        $ten = $san_pham->ma_san_pham . '_' . Str::slug(($validated['ten_mau'] ?? ''));
        $time = now()->format('Ymd_His');
        $unique = uniqid();
        $ext = $file->getClientOriginalExtension();
        $filename = "{$ten}_{$time}_{$unique}.{$ext}";

        $path = $file->storeAs('anh_san_phams', $filename, 'public');

        $list_ct_ids = DB::table('san_pham_chi_tiet')
          ->where('id_san_pham', $id_san_pham)
          ->where('id_mau', $request->id_mau)
          ->pluck('id_san_pham_chi_tiet')
          ->toArray();

        foreach ($list_ct_ids as $ct_id) {
          AnhSanPham::create([
            'id_san_pham_chi_tiet' => $ct_id,
            'anh_url' => $path,
          ]);
        }
      }
    }
    return redirect()->back()->with('success', 'Thêm thuộc tính chi tiết thành công');
  }


  public function update(Request $request, $id_san_pham)
  {
    // =========================
    // 1. VALIDATE
    // =========================
    $validated = $request->validate([
      'ten_mau' => 'nullable|string',

      // ảnh mới
      'files_anh_san_pham_new' => 'nullable|array',
      'files_anh_san_pham_new.*.file' => 'required|mimes:jpg,jpeg,png,webp|max:2048',
      'files_anh_san_pham_new.*.thu_tu' => 'required|integer|min:1',

      // ảnh cũ
      'path_anh_san_pham_old' => 'nullable|array',
      'path_anh_san_pham_old.*.id_anh_san_pham' => 'required|integer',
      'path_anh_san_pham_old.*.thu_tu' => 'required|integer|min:1',
    ], [
      'files_anh_san_pham_new.*.thu_tu.required' => 'Chưa thêm thứ tự ảnh mới.',
      'path_anh_san_pham_old.*.thu_tu.required' => 'Chưa thêm thứ tự ảnh cũ.',
    ]);

    // =========================
    // 2. CHECK TRÙNG THỨ TỰ
    // =========================
    $thuTuCu = collect($request->path_anh_san_pham_old ?? [])->pluck('thu_tu');
    $thuTuMoi = collect($request->files_anh_san_pham_new ?? [])->pluck('thu_tu');

    $allThuTu = $thuTuCu->merge($thuTuMoi);

    if ($allThuTu->count() !== $allThuTu->unique()->count()) {
      throw ValidationException::withMessages([
        'thu_tu' => 'Thứ tự ảnh không được trùng nhau.',
      ]);
    }

    // =========================
    // 3. TRANSACTION
    // =========================
    DB::transaction(function () use ($request, $validated, $id_san_pham) {

      // ---------- ẢNH CŨ ----------
      $dsAnhCu = AnhSanPham::where(
        'id_san_pham_chi_tiet',
        $request->id_san_pham_chi_tiet
      )->get();

      $idsGiuLai = collect($request->path_anh_san_pham_old ?? [])
        ->pluck('id_anh_san_pham')
        ->toArray();

      foreach ($dsAnhCu as $anh) {
        if (!in_array($anh->id_anh_san_pham, $idsGiuLai)) {
          Storage::disk('public')->delete($anh->anh_url);
          $anh->delete();
        } else {
          $data = collect($request->path_anh_san_pham_old)
            ->firstWhere('id_anh_san_pham', $anh->id_anh_san_pham);

          $anh->update(['thu_tu' => $data['thu_tu']]);
        }
      }

      // ---------- ẢNH MỚI ----------
      if ($request->has('files_anh_san_pham_new')) {
        $sanPham = SanPham::findOrFail($id_san_pham);

        $ctIds = DB::table('san_pham_chi_tiet')
          ->where('id_san_pham', $id_san_pham)
          ->where('id_mau', $request->id_mau)
          ->pluck('id_san_pham_chi_tiet')
          ->toArray();

        foreach ($request->files_anh_san_pham_new as $item) {
          $file = $item['file'];

          $filename = sprintf(
            '%s_%s_%s.%s',
            $sanPham->ma_san_pham,
            Str::slug($validated['ten_mau'] ?? 'mau'),
            uniqid(),
            $file->getClientOriginalExtension()
          );

          $path = $file->storeAs('anh_san_phams', $filename, 'public');

          foreach ($ctIds as $ctId) {
            AnhSanPham::create([
              'id_san_pham_chi_tiet' => $ctId,
              'anh_url' => $path,
              'thu_tu' => $item['thu_tu'],
            ]);
          }
        }
      }
    });

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
