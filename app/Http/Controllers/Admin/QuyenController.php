<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quyen;
use Illuminate\Http\Request;

class QuyenController extends Controller
{
    public function dsQuyen()
    {
        $quyen = new Quyen();
        return response()->json([ 'data' => $quyen::all(), 'message' => '', 'status' => 200]);
    }

    public function them(Request $r)
    {
        $ten_quyen = $r->ten_quyen;
        $result = Quyen::insert([
            'ten_quyen' => $ten_quyen,
        ]);

        if(!$result){
            return response()->json([
                'message' => 'Thêm thất bại',
                'status' => 400,
            ]);
        }

        return response()->json([
            'message' => 'Thêm thành công',
            'status' => 200,
        ]);
    }

    public function capNhat(Request $r)
    {


    }


}
