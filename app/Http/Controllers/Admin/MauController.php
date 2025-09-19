<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MauController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/mau/mau', [
            'maus' => Mau::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|string|max:255'
        ], [
            'ten_mau.required' => 'Tên màu không được để trống'
        ]);

        Mau::create($validated);
        return redirect()
            ->back()
            ->with('success', 'Tạo thành công');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|string|max:255'
        ], [
            'ten_mau.required' => 'Tên màu không được để trống'
        ]);

        $mau = Mau::findOrFail($request->id_mau);

        $mau->update($validated);
        return redirect()
            ->back()
            ->with('success', 'cập nhật thành công');
    }

    public function destroy(Request $request)
    {
        $mau = Mau::findOrFail($request->id_mau);

        $mau->delete();
        return redirect()
            ->back()
            ->with('success', 'Xóa thành công');
    }
}
