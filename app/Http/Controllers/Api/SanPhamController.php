<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Response;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function getSanPham(Request $request){
        $products = SanPham::pagination(env('ITEM_PER_PAGE'));
        return Response::success($products, '');
    }
}
