<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThanhToanController extends Controller
{
  public function index()
  {
    return Inertia::render('admin/nhap-hang/nhap-hang', [
      'nhap_hangs' => NhapHang::all(),
    ]);
  }
}
