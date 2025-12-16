<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuocTinhController extends Controller
{
    public function getFilterThuocTinh()
    {
        $data = DB::table('thuoc_tinh as tt')
            ->join('thuoc_tinh_chi_tiet as ttct', 'tt.id_thuoc_tinh', '=', 'ttct.id_thuoc_tinh')
            ->select(
                'tt.id_thuoc_tinh',
                'tt.ten_thuoc_tinh',
                'ttct.id_thuoc_tinh_chi_tiet',
                'ttct.ten_thuoc_tinh_chi_tiet'
            )
            ->orWhere('tt.ten_thuoc_tinh', 'Trọng lượng')
            ->orWhere('tt.ten_thuoc_tinh', 'Điểm cân bằng')
            ->orWhere('tt.ten_thuoc_tinh', 'Phong cách chơi')
            ->get();

        $formattedAttributes = $data->groupBy('id_thuoc_tinh')->map(function ($group) {
            $firstItem = $group->first();
            return [
                'id_thuoc_tinh' => $firstItem->id_thuoc_tinh,
                'ten_thuoc_tinh' => $firstItem->ten_thuoc_tinh,
                'values' => $group->map(function ($item) {
                    return [
                        'id_thuoc_tinh_chi_tiet' => $item->id_thuoc_tinh_chi_tiet,
                        'ten_thuoc_tinh_chi_tiet' => $item->ten_thuoc_tinh_chi_tiet,
                    ];
                })->values()
            ];
        })->values();

        return Response::Success($formattedAttributes, '');
    }
}
