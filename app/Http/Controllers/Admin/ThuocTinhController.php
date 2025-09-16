<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuocTinh;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThuocTinhController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/thuoc-tinh/thuoc-tinh', [
            'thuoc_tinhs' => ThuocTinh::all()
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuoc_tinh' => 'required|string'
        ], [
            'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.'
        ]);

        ThuocTinh::create($validatedData);

        return redirect()->route('thuoc_tinh');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'ten_thuoc_tinh' => 'required|string'
        ], [
            'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.'
        ]);
        $thuoc_tinh = ThuocTinh::findOrFail($request->id_thuoc_tinh);

        $thuoc_tinh->update($validatedData);

        return redirect()->route('thuoc_tinh');
    }

    public function destroy(Request $request)
    {
        $thuoc_tinh = ThuocTinh::findOrFail($request->id_thuoc_tinh);
        $thuoc_tinh->delete();

        return redirect()->route('thuoc_tinh');
    }
}
