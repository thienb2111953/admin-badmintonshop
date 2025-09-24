<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function dsSanPham()
    {
        $san_pham = SanPham::all();
        return response()->json([
            'status' => 'success',
            'data' => $san_pham
        ], 200);
    }
}
