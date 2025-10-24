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
        $query = DB::table('san_pham')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->join('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->join('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham', '=', 'san_pham.id_san_pham')
            ->join('anh_san_pham', 'anh_san_pham.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.slug as slug_san_pham',
                'san_pham.gia_niem_yet',
                'san_pham.gia_ban',
                'anh_san_pham.anh_url'
            ])
            ->where('anh_san_pham.thu_tu', 1)
            ->orderBy('san_pham.created_at', 'desc');

        $products = $query->limit(10)->get();
        $rackets = $query->where('danh_muc.slug', 'vot-cau-long')->limit(10)->get();
        $popular = $query->limit(10)->get();
        $shoes = $query->where('danh_muc.slug', 'giay-cau-long')->limit(10)->get();
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
