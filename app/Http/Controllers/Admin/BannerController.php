<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banner = DB::table('banner')->get();
        return Inertia::render('admin/banner/banner', [
            'banners' => $banner,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'thu_tu' => 'nullable|integer|unique:banner,thu_tu',
            'href' => 'required|string',
            'file_logo' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'thu_tu.integer' => "Thứ tự phải là số nguyên",
            'thu_tu.unique' => "Thứ tự đã bị trùng lặp",
            'href.string' => "Liên kết phải là chuỗi ký tự",
            'href.required' => "Đường dẫn không được để trống",
            'file_logo.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png, webp',
            'file_logo.max' => 'Kích thước ảnh tối đa 2MB.',
        ]);
        if ($request->hasFile('file_logo')) {
            $file = $request->file('file_logo');
            $ten = Str::slug("Banner");
            $time = now()->format('Ymd_His');
            $ext = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";
            $path = $file->storeAs('banner', $filename, 'public');
            $validated['img_url'] = $path;
        }

        DB::table('banner')->insert([
            'img_url' => $validated['img_url'],
            'thu_tu' => $validated['thu_tu'] ?? 0,
            'href' => $validated['href'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Thêm banner thành công');
    }

    public function update(Request $request)
    {

        $validated = $request->validate([
            'img_url' => 'required|string|',
            'thu_tu' => 'nullable|integer',
            'href' => 'required|string',
            'file_logo' => 'nullable|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'img_url.required' => "Ảnh không được để trống",
            'img_url.string' => "Đường dẫn ảnh phải là chuỗi ký tự",
            'thu_tu.integer' => "Thứ tự phải là số nguyên",
            'href.string' => "Liên kết phải là chuỗi ký tự",
            'href.required' => "Đường dẫn không được để trống",
            'file_logo.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png, webp',
            'file_logo.max' => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $banner = Banner::findOrFail($request->id_banner);

        if ($request->hasFile('file_logo')) {
            $file_old = $banner->logo_url;
            if ($file_old && Storage::disk('public')->exists($file_old)) {
                Storage::disk('public')->delete($file_old);
            }
            $file = $request->file('file_logo');
            $ten = Str::slug("Banner");
            $time = now()->format('Ymd_His');
            $ext = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";
            $path = $file->storeAs('banner', $filename, 'public');
            $validated['img_url'] = $path;
        }


        // ✅ Lấy thứ tự cũ
        $old_thu_tu = $banner->thu_tu ?? 0;
        $new_thu_tu = $validated['thu_tu'] ?? 0;

        // ✅ Nếu người dùng đổi sang một thứ tự đã tồn tại
        if ($new_thu_tu != $old_thu_tu) {
            $conflict = DB::table('banner')
                ->where('thu_tu', $new_thu_tu)
                ->where('id_banner', '!=', $banner->id_banner)
                ->first();

            if ($conflict) {
                // 🔁 Hoán đổi thứ tự giữa 2 banner
                DB::table('banner')->where('id_banner', $conflict->id_banner)
                    ->update(['thu_tu' => $old_thu_tu]);
            }
        }

        // ✅ Nếu người dùng đổi sang một thứ tự đã tồn tại
        if ($new_thu_tu != $old_thu_tu) {
            $conflict = DB::table('banner')
                ->where('thu_tu', $new_thu_tu)
                ->where('id_banner', '!=', $banner->id_banner)
                ->first();

            if ($conflict) {
                // 🔁 Hoán đổi thứ tự giữa 2 banner
                DB::table('banner')
                    ->where('id_banner', $conflict->id_banner)
                    ->update(['thu_tu' => $old_thu_tu]);
            }
        }

        DB::table('banner')
            ->where('id_banner', $request->id_banner) // hoặc $request->id_banner tùy tên cột khóa chính của bạn
            ->update([
                'img_url' => $validated['img_url'],
                'thu_tu' => $validated['thu_tu'] ?? 0,
                'href' => $validated['href'] ?? null,
                'updated_at' => now(),
            ]);

        return redirect()->route('banner')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        // ✅ Lấy thứ tự của banner sắp bị xóa
        $deletedThuTu = DB::table('banner')->where('id_banner', $request->id_banner)->value('thu_tu');

        // ✅ Xóa banner
        DB::table('banner')->where('id_banner', $request->id_banner)->delete();

        // ✅ Giảm thứ tự của tất cả banner có thứ tự lớn hơn
        DB::table('banner')->where('thu_tu', '>', $deletedThuTu)->decrement('thu_tu');

        return redirect()->route('banner')->with('success', 'Xóa thành công và đã cập nhật lại thứ tự');
    }
}
