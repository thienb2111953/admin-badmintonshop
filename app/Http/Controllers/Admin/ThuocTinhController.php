<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuocTinh;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;


class ThuocTinhController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/thuoc-tinh/thuoc-tinh', [
            'thuoc_tinhs' => ThuocTinh::all()
        ]);
    }

//    public function store(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'ten_thuoc_tinh' => 'required|string|max:255'
//        ], [
//            'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.'
//        ]);
//
//        if ($validator->fails()) {
//            return Inertia::render('admin/thuoc-tinh/thuoc-tinh', [
//                'thuoc_tinhs' => ThuocTinh::all(),
//                'errors' => $validator->errors()->getMessages()
//            ])->withViewData([
//                'errors' => $validator->errors()
//            ]);
//        }
//
//        ThuocTinh::create($validator->validated());
//
//        return redirect()->back();
//    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_thuoc_tinh' => 'required|string|max:255'
        ], [
            'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.'
        ]);

        ThuocTinh::create($validated);

        return back()->with('success', 'Thêm thành công');
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

        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $thuoc_tinh = ThuocTinh::findOrFail($request->id_thuoc_tinh);
        $thuoc_tinh->delete();

        return redirect()->back();
    }
}
