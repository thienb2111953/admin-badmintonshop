<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThuongHieu;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuongHieuController extends Controller
{
    public function getAllThuongHieu()
    {
        $data = DB::table('thuong_hieu')->pluck('ten_thuong_hieu');
        return Response::Success($data, 'Get data successfully.');
    }
}
