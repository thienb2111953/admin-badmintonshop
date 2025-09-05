<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quyen;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class QuyenController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/quyen', [
            'quyen' => Quyen::all()
        ]);
    }

    public function dsQuyen()
    {
        return Response()->json([
            'data' => Quyen::all(),
            'message' => '',
            'status' => 200,
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'ten_quyen' => 'required|string',
        ], [
            'ten_quyen.required' => 'Tên quyền không được để trống.',
        ]);

        if ($request->id_quyen && $request->id_quyen != 0) {
            $quyen = Quyen::findOrFail($request->id_quyen);
            $quyen->update($validatedData);
        } else {
            Quyen::create($validatedData);
        }

        return redirect()->route('quyen.index')->with('success', 'Lưu quyền thành công!');
    }

    public function destroy(Request $r)
    {
        $quyen = Quyen::find($r->id_quyen);
        if ($quyen) {
            $quyen->delete();
            return redirect()->back()->with('success', 'Xóa quyền thành công!');
        }

        return redirect()->back()->with('error', 'Quyền không tồn tại!');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []); // Lấy danh sách id từ request

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Chưa chọn quyền nào để xóa!');
        }

        // Xóa tất cả quyền có id trong danh sách
        $deletedCount = Quyen::whereIn('id_quyen', $ids)->delete();

        if ($deletedCount > 0) {
            return redirect()->back()->with('success', "Đã xóa $deletedCount quyền thành công!");
        }

        return redirect()->back()->with('error', 'Xóa thất bại hoặc quyền không tồn tại!');
    }
}
