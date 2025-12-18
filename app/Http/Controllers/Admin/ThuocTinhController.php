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

  public function store(Request $request)
  {
    $validated = $request->validate([
      'ten_thuoc_tinh' => 'required|string|max:255|unique:thuoc_tinh,ten_thuoc_tinh'
    ], [
      'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.',
      'ten_thuoc_tinh.unique' => 'Tên thuộc tính đã tồn tại.',
      'ten_thuoc_tinh.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.'
    ]);

    ThuocTinh::create($validated);

    return redirect()->back();
  }

  public function update(Request $request)
  {
    $validatedData = $request->validate([
      'ten_thuoc_tinh' => 'required|string|max:255|unique:thuoc_tinh,ten_thuoc_tinh'
    ], [
      'ten_thuoc_tinh.required' => 'Tên thuộc tính không được để trống.',
      'ten_thuoc_tinh.unique' => 'Tên thuộc tính đã tồn tại.',
      'ten_thuoc_tinh.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.'
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
