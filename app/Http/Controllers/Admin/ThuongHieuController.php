<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThuongHieuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('admin/thuong-hieu/thuong-hieu', [
            'thuong_hieu' => ThuongHieu::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuong_hieu' => 'required|string',
            'ma_thuong_hieu' => 'required|string',
            'logo_url' => 'string',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
        ]);

        ThuongHieu::create($validatedData);

        return redirect()->route('thuong_hieu');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuong_hieu' => 'required|string',
            'ma_thuong_hieu' => 'required|string',
            'logo_url' => 'string',
        ], [
            'ten_thuong_hieu.required' => 'Tên Thương hiệu không được để trống.',
            'ma_thuong_hieu.required' => 'Mã Thương hiệu không được để trống.',
        ]);

        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);
        $thuong_hieu->update($validatedData);

        return redirect()->route('thuong_hieu');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'id_thuong_hieu' => 'required|integer|exists:quyen,id_thuong_hieu',
        ], [
            'id_thuong_hieu.required' => 'ID thương hiệu không hợp lệ.',
        ]);

        $thuong_hieu = ThuongHieu::findOrFail($request->id_thuong_hieu);
        $thuong_hieu->delete();

        return redirect()->route('thuong_hieu');
    }
}
