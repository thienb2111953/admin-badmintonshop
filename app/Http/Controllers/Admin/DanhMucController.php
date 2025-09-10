<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DanhMucController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $danh_muc = DanhMuc::all();
        return Inertia::render('admin/danh-muc/danh-muc', [
            'danh_mucs' => $danh_muc,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);
        DanhMuc::create([
            ...$validated,
            // id_user => auth()->id('id_user'),
        ]);
        return redirect()->route('danh-muc.danh-muc')->with('success', 'Tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(DanhMuc $danhMuc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DanhMuc $danhMuc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DanhMuc $danhMuc)
    {
        $validated = $request->validate([
            'ten_danh_muc' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);
        $danhMuc->update($validated);
        return redirect()->route('danh-muc.danh-muc')->with('success', 'Cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanhMuc $danhMuc)
    {
        $danhMuc->delete();
        return redirect()->route('danh-muc.danh-muc')->with('success', 'Xóa thành công');
    }
}
