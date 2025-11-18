<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    public function getViewHome()
    {
        $variantSubquery = DB::table('san_pham_chi_tiet as sct')
            ->join('anh_san_pham as asp', 'asp.id_san_pham_chi_tiet', '=', 'sct.id_san_pham_chi_tiet')
            ->select(
                'sct.id_san_pham',
                'sct.id_san_pham_chi_tiet',
                'sct.gia_niem_yet',
                'sct.gia_ban',
                'asp.anh_url',
                DB::raw('MIN(sct.gia_ban) OVER(PARTITION BY sct.id_san_pham) as gia_thap_nhat'),
                DB::raw('MAX(sct.gia_ban) OVER(PARTITION BY sct.id_san_pham) as gia_cao_nhat'),
                DB::raw('ROW_NUMBER() OVER(
                PARTITION BY sct.id_san_pham
                ORDER BY sct.gia_ban ASC
            ) as rn')
            )
            ->where('asp.thu_tu', 1);

        $baseQuery = DB::table('san_pham')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->join('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->join('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->joinSub($variantSubquery, 'variants', function ($join) {
                $join->on('san_pham.id_san_pham', '=', 'variants.id_san_pham')
                    ->where('variants.rn', '=', 1);
            })
            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.slug',
                'variants.gia_niem_yet',
                'variants.gia_ban',
                'variants.gia_thap_nhat',
                'variants.gia_cao_nhat',
                'variants.anh_url'
            ])
            ->orderBy('san_pham.created_at', 'desc');

        $products = (clone $baseQuery)->limit(10)->get();

        $rackets = (clone $baseQuery)
            ->where('danh_muc.slug', 'vot-cau-long')
            ->limit(10)
            ->get();

        $popular = (clone $baseQuery)->limit(10)->get();

        $shoes = (clone $baseQuery)
            ->where('danh_muc.slug', 'giay-cau-long')
            ->limit(10)
            ->get();

        $banner = DB::table('banner')
            ->select([
                'id_banner',
                'img_url',
                'thu_tu',
                'href'
            ])
            ->orderBy('thu_tu', 'asc')
            ->get();

        return Response::Success([
            'products' => $products,
            'rackets' => $rackets,
            'popular' => $popular,
            'shoes' => $shoes,
            'banner' => $banner
        ]);
    }
}
