<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhMucController extends Controller
{
    public function getDanhMuc(Request $request)
    {
        $query = DB::table('danh_muc')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc', '=', 'danh_muc.id_danh_muc')
            ->select(
                'danh_muc.id_danh_muc as dm_id',
                'danh_muc.ten_danh_muc as dm_ten',
                'danh_muc.slug as dm_slug',
                'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu as th_id',
                'danh_muc_thuong_hieu.ten_danh_muc_thuong_hieu as th_ten',
                'danh_muc_thuong_hieu.slug as th_slug'
            )
            ->get();

        $danh_muc = $query->groupBy('dm_id')->map(function ($items) {
            $first = $items->first();
            return [
                'id_danh_muc' => $first->dm_id,
                'ten_danh_muc' => $first->dm_ten,
                'slug_danh_muc' => $first->dm_slug,
                'danh_muc_thuong_hieu' => $items->map(function ($item) {
                    return [
                        'id_danh_muc_thuong_hieu' => $item->th_id,
                        'ten_danh_muc_thuong_hieu' => $item->th_ten,
                        'slug_danh_muc_thuong_hieu' => $item->th_slug,
                    ];
                })->values()
            ];
        })->values();

        if ($danh_muc) {
            return Response::Success($danh_muc, '');
        }
        return Response::Error('Lỗi phát sinh !', 'Lỗi');
    }
}
