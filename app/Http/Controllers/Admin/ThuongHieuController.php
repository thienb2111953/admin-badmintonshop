<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ThuongHieuController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/thuong-hieu/thuong-hieu', [
            'thuong_hieus' => ThuongHieu::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuong_hieu' => 'required|string',
            'logo_url'        => 'nullable|string',
            'file_logo'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
            'file_logo.mimes'           => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'file_logo.max'             => 'Kích thước ảnh tối đa 2MB.',
        ]);

        if ($request->hasFile('file_logo')) {
            $file = $request->file('file_logo');
            $ten = Str::slug($request->ten_thuong_hieu);
            $time = now()->format('Ymd_His');
            $ext = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";
            $path = $file->storeAs('logos_thuong_hieu', $filename, 'public');
            $validatedData['logo_url'] = $path;
        }

        ThuongHieu::create($validatedData);

        return redirect()->route('thuong_hieu');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuong_hieu' => 'required|string',
            'logo_url'        => 'nullable|string',
            'file_logo'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
            'file_logo.mimes'           => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'file_logo.max'             => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);

        if ($request->hasFile('file_logo')) {
            $file_old = $thuong_hieu->logo_url;
            if ($file_old && Storage::disk('public')->exists($file_old)) {
                Storage::disk('public')->delete($file_old);
            }
            $file = $request->file('file_logo');
            $ten = Str::slug($request->ten_thuong_hieu);
            $time = now()->format('Ymd_His');
            $ext = $file->getClientOriginalExtension();
            $filename = "{$ten}_{$time}.{$ext}";
            $path = $file->storeAs('logos_thuong_hieu', $filename, 'public');
            $validatedData['logo_url'] = $path;
        }

        $thuong_hieu->update($validatedData);

        return redirect()->route('thuong_hieu');
    }

    public function destroy(Request $request)
    {
        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);

        $file_old = $thuong_hieu->logo_url;
        if ($file_old && Storage::disk('public')->exists($file_old)) {
            Storage::disk('public')->delete($file_old);
        }

        $thuong_hieu->delete();

        return redirect()->route('thuong_hieu');
    }
}
