<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
            'ma_thuong_hieu' => 'required|string',
            'logo_url'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
            'logo_url.mimes'           => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'logo_url.max'             => 'Kích thước ảnh tối đa 2MB.',
        ]);

        if ($request->hasFile('logo_url')) {
            $path = $request->file('logo_url')->store('logos_thuong_hieu', 'public');
            $validatedData['logo_url'] = $path;
        }

        ThuongHieu::create($validatedData);

        return redirect()->route('thuong_hieu');
    }

    public function update(Request $request)
    {
        dd($request);
        $validatedData = $request->validate([
            'ten_thuong_hieu' => 'required|string',
            'ma_thuong_hieu' => 'required|string',
            'logo_url'        => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
            'logo_url.mimes'           => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'logo_url.max'             => 'Kích thước ảnh tối đa 2MB.',
        ]);

        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);


        if ($request->hasFile('logo_url')) {
            if ($thuong_hieu->logo_url && Storage::disk('public')->exists($thuong_hieu->logo_url)) {
                Storage::disk('public')->delete($thuong_hieu->logo_url);
            }

            $path = $request->file('logo_url')->store('logos_thuong_hieu', 'public');
            $validatedData['logo_url'] = $path;
        }

        $thuong_hieu->update($validatedData);

        return redirect()->route('thuong_hieu');
    }

    public function destroy(Request $request)
    {
        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);
        $thuong_hieu->delete();

        return redirect()->route('thuong_hieu');
    }
}
