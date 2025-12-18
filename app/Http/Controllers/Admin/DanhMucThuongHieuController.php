<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\DanhMucThuocTinh;
use App\Models\DanhMucThuongHieu;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;

class DanhMucThuongHieuController extends Controller
{
  public function exportSanPham()
  {
    $scriptPath = storage_path('app/python/export_san_pham.py');
    $pythonPath = env('PYTHON_PATH', 'python'); // Lấy đường dẫn exe đã cấu hình

    if (!file_exists($scriptPath)) {
      return response()->json(['message' => 'File script không tồn tại!'], 404);
    }

    // Câu lệnh chạy (như cũ)
    $command = "\"{$pythonPath}\" \"{$scriptPath}\"";

    // --- KHẮC PHỤC Ở ĐÂY ---
    // Sử dụng phương thức env() của Process để GHI ĐÈ cấu hình
    $result = Process::env([
      // Ép buộc dùng localhost thay vì 172.22.166.22
      'DB_HOST' => '127.0.0.1',

      // Đảm bảo các thông số khác lấy đúng từ Laravel
      'DB_DATABASE' => env('DB_DATABASE'),
      'DB_USERNAME' => env('DB_USERNAME'),
      'DB_PASSWORD' => env('DB_PASSWORD'),
      'DB_PORT' => env('DB_PORT', '5432'),

      // Giữ lại các biến môi trường hệ thống (System Root, Path...) để Python chạy được
      'SYSTEMROOT' => getenv('SYSTEMROOT'),
      'PATH' => getenv('PATH'),
      'TEMP' => getenv('TEMP'),

      'PYTHONIOENCODING' => 'utf-8',
      'LANG' => 'C.UTF-8',
    ])->run($command);

    if ($result->successful()) {
      return response()->json(['message' => 'Thành công!', 'output' => $result->output()]);
    } else {
      return response()->json(['message' => 'Lỗi: ' . $result->errorOutput()], 500);
    }
  }

  public function index()
  {
    $danh_mucs = DanhMuc::all();
    $thuong_hieus = ThuongHieu::all();
    $danh_muc_thuong_hieus = DB::table('danh_muc_thuong_hieu')
      ->join('danh_muc', 'danh_muc_thuong_hieu.id_danh_muc', '=', 'danh_muc.id_danh_muc')
      ->join('thuong_hieu', 'danh_muc_thuong_hieu.id_thuong_hieu', '=', 'thuong_hieu.id_thuong_hieu')
      ->select(
        'danh_muc_thuong_hieu.*',
        'danh_muc.ten_danh_muc as ten_danh_muc',
        'thuong_hieu.ten_thuong_hieu as ten_thuong_hieu',
      )
      ->orderBy('danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', 'desc')
      ->get();

    return Inertia::render('admin/danh-muc-thuong-hieu/danh-muc-thuong-hieu', [
      // 'thuong_hieu_info' => ThuongHieu::find($id_thuong_hieu),
      'danh_muc_thuong_hieus' => $danh_muc_thuong_hieus,
      'danh_mucs' => $danh_mucs,
      'thuong_hieus' => $thuong_hieus,
    ]);
  }

  public function storeView()
  {
    $danh_mucs = DanhMuc::all();
    $thuong_hieus = ThuongHieu::all();

    return Inertia::render('admin/danh-muc-thuong-hieu/edit-page', [
      'danh_mucs' => $danh_mucs,
      'thuong_hieus' => $thuong_hieus,
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate(
      [
        'ten_danh_muc_thuong_hieu' => 'required|string|max:255',
        'slug'                    => 'required|string|max:255|unique:danh_muc_thuong_hieu,slug',
        'mo_ta'                   => 'nullable|string',
        'id_danh_muc'             => 'required|integer|exists:danh_muc,id_danh_muc',
        'id_thuong_hieu'          => 'required|integer|exists:thuong_hieu,id_thuong_hieu',
      ],
      [
        'ten_danh_muc_thuong_hieu.required' => 'Tên danh mục thương hiệu là bắt buộc.',
        'ten_danh_muc_thuong_hieu.string'   => 'Tên danh mục thương hiệu phải là chuỗi.',
        'ten_danh_muc_thuong_hieu.max'      => 'Vượt quá số ký tự quy định (255 ký tự).',

        'slug.required' => 'Slug không được để trống.',
        'slug.string'   => 'Slug phải là chuỗi.',
        'slug.max'      => 'Slug tối đa 255 ký tự.',
        'slug.unique'   => 'Slug đã tồn tại.',

        'mo_ta.string' => 'Mô tả phải là chuỗi.',

        'id_danh_muc.required' => 'Vui lòng chọn danh mục.',
        'id_danh_muc.integer'  => 'Danh mục không hợp lệ.',
        'id_danh_muc.exists'   => 'Danh mục không tồn tại.',

        'id_thuong_hieu.required' => 'Vui lòng chọn thương hiệu.',
        'id_thuong_hieu.integer'  => 'Thương hiệu không hợp lệ.',
        'id_thuong_hieu.exists'   => 'Thương hiệu không tồn tại.',
      ]
    );

    DanhMucThuongHieu::create($validated);

    return redirect()->route('san_pham_thuong_hieu')->with('success', 'Tạo thành công');
  }

  public function updateView($id_danh_muc_thuong_hieu)
  {
    $danh_mucs = DanhMuc::all();
    $thuong_hieus = ThuongHieu::all();
    $danh_muc_thuong_hieu = DanhMucThuongHieu::findOrFail($id_danh_muc_thuong_hieu);

    return Inertia::render('admin/danh-muc-thuong-hieu/edit-page', [
      'danh_mucs' => $danh_mucs,
      'thuong_hieus' => $thuong_hieus,
      'danh_muc_thuong_hieu' => $danh_muc_thuong_hieu,
    ]);
  }

  public function update(Request $request)
  {
    $id = $request->id_danh_muc_thuong_hieu;

    $validated = $request->validate(
      [
        'ten_danh_muc_thuong_hieu' => 'required|string|max:255',
        'slug' => [
          'required',
          'string',
          'max:255',
          Rule::unique('danh_muc_thuong_hieu', 'slug')
            ->ignore($id, 'id_danh_muc_thuong_hieu'),
        ],
        'mo_ta' => 'nullable|string',
        'id_danh_muc' => 'required|integer|exists:danh_muc,id_danh_muc',
        'id_thuong_hieu' => 'required|integer|exists:thuong_hieu,id_thuong_hieu',
      ],
      [
        'ten_danh_muc_thuong_hieu.required' => 'Tên danh mục thương hiệu là bắt buộc.',
        'ten_danh_muc_thuong_hieu.max' => 'Vượt quá số ký tự quy định (255 ký tự).',

        'slug.required' => 'Slug không được để trống.',
        'slug.unique' => 'Slug đã tồn tại.',

        'id_danh_muc.required' => 'Vui lòng chọn danh mục.',
        'id_danh_muc.exists' => 'Danh mục không tồn tại.',

        'id_thuong_hieu.required' => 'Vui lòng chọn thương hiệu.',
        'id_thuong_hieu.exists' => 'Thương hiệu không tồn tại.',
      ]
    );

    $record = DanhMucThuongHieu::findOrFail($id);
    $record->update($validated);

    return redirect()->route('san_pham_thuong_hieu')->with('success', 'Cập nhật thành công');
  }

  public function destroy(Request $request)
  {
    $danh_muc_thuoc_tinh = DanhMucThuongHieu::findOrFail($request->id_danh_muc_thuong_hieu);

    $danh_muc_thuoc_tinh->delete();

    return redirect()->back()->with('success', 'Xóa thành công');
  }
}
