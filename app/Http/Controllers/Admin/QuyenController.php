<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quyen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuyenController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/quyen/quyen', [
            'quyen' => Quyen::all(),
        ]);
    }

    public function dsQuyen()
    {
        return response()->json([
            'data' => Quyen::all(),
            'message' => '',
            'status' => 200,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ten_quyen' => 'required|string',
        ], [
            'ten_quyen.required' => 'Tên quyền không được để trống.',
        ]);

        Quyen::create($validatedData);

        return redirect()->route('quyen');
    }


    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id_quyen'  => 'required|integer|exists:quyen,id_quyen',
            'ten_quyen' => 'required|string',
        ], [
            'id_quyen.required' => 'ID quyền không hợp lệ.',
            'ten_quyen.required' => 'Tên quyền không được để trống.',
        ]);

        $quyen = Quyen::findOrFail($request->id_quyen);
        $quyen->update($validatedData);

        return redirect()->route('quyen');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id_quyen' => 'required|integer|exists:quyen,id_quyen',
        ], [
            'id_quyen.required' => 'ID quyền không hợp lệ.',
        ]);

        $quyen = Quyen::findOrFail($request->id_quyen);
        $quyen->delete();

        return redirect()->route('quyen');
    }


    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'message' => 'Chưa chọn quyền nào để xóa!',
                'status' => 400,
            ], 400);
        }

        $deletedCount = Quyen::whereIn('id_quyen', $ids)->delete();

        return redirect()->route('quyen');
    }
}
