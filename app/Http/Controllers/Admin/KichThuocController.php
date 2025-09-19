<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KichThuoc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KichThuocController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/kich-thuoc/kich-thuoc', [
            'kich_thuocs' => KichThuoc::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_kich_thuoc' => 'required|string|max:255'
        ], [
            'ten_kich_thuoc.required' => 'Tên kích thước không được để trống'
        ]);

        KichThuoc::create($validated);
        return redirect()
            ->back()
            ->with('success', 'Tạo thành công');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten_kich_thuoc' => 'required|string|max:255'
        ], [
            'ten_kich_thuoc.required' => 'Tên kích thước không được để trống'
        ]);

        $kich_thuoc = KichThuoc::findOrFail($request->id_kich_thuoc);

        $kich_thuoc->update($validated);
        return redirect()
            ->back()
            ->with('success', 'cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        $kich_thuoc = KichThuoc::findOrFail($request->id_kich_thuoc);

        $kich_thuoc->delete();
        return redirect()
            ->back()
            ->with('success', 'Xóa thành công');
    }
}
