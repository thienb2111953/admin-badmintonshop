<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaiDat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaiDatController extends Controller
{
    public function index()
    {
        $cai_dat = CaiDat::all();
        return Inertia::render('admin/cai-dat/cai-dat', [
            'cai_dats' => $cai_dat,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_cai_dat'   => 'required|string|max:255',
            'gia_tri'           => 'nullable|string|max:255',
        ], [
            'ten_cai_dat.required' => "Tên không được để trống"
        ]);

        $cai_dat = CaiDat::create([
            'ten_cai_dat' => $validated['ten_cai_dat'],
            'gia_tri'         => $validated['gia_tri'],
        ]);

        return redirect()->back()->with('success', 'Tạo thành công');
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten_cai_dat'   => 'required|string|max:255',
            'gia_tri'           => 'nullable|string|max:255',
        ], [
            'ten_cai_dat.required' => "Tên không được để trống"
        ]);

        $cai_dat = CaiDat::findOrFail($request->id_cai_dat);
        $cai_dat->update([
            'ten_cai_dat' => $validated['ten_cai_dat'],
            'gia_tri'         => $validated['gia_tri'],
        ]);
        return redirect()->route('cai_dat')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        $cai_dat = CaiDat::findOrFail($request->id_cai_dat);
        $cai_dat->delete();
        return redirect()->route('cai_dat')->with('success', 'Xóa thành công');
    }
}
