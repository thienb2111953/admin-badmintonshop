<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrangChuController extends Controller
{
    public function buildBaseProductQuery()
    {
        $now = now();

        // 1. Subquery lấy biến thể (Giữ nguyên)
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
                DB::raw('ROW_NUMBER() OVER(PARTITION BY sct.id_san_pham ORDER BY sct.gia_ban ASC) as rn')
            )
            ->where('asp.thu_tu', 1);

        // 2. Subquery lấy Khuyến mãi hợp lệ (Giữ nguyên)
        $promoSubquery = DB::table('san_pham_khuyen_mai as spkm')
            ->join('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'spkm.id_khuyen_mai')
            ->select(
                'spkm.id_san_pham',
                'km.ten_khuyen_mai',
                'km.gia_tri',
                'km.don_vi_tinh',
                DB::raw('ROW_NUMBER() OVER(PARTITION BY spkm.id_san_pham ORDER BY km.gia_tri DESC) as rn_promo')
            )
            ->where('km.ngay_bat_dau', '<=', $now)
            ->where('km.ngay_ket_thuc', '>=', $now);

        // 3. Trả về Query Builder đã join đầy đủ
        return DB::table('san_pham')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->join('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->join('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->joinSub($variantSubquery, 'variants', function ($join) {
                $join->on('san_pham.id_san_pham', '=', 'variants.id_san_pham')
                    ->where('variants.rn', '=', 1);
            })
            ->leftJoinSub($promoSubquery, 'promos', function ($join) {
                $join->on('san_pham.id_san_pham', '=', 'promos.id_san_pham')
                    ->where('promos.rn_promo', '=', 1);
            })
            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.slug',
                // Các cột cần cho Search cũng giống Home
                'danh_muc.ten_danh_muc',
                'thuong_hieu.ten_thuong_hieu',

                'variants.gia_niem_yet',
                'variants.gia_ban',
                'variants.gia_thap_nhat',
                'variants.gia_cao_nhat',
                'variants.anh_url',
                'promos.ten_khuyen_mai',
                'promos.gia_tri as km_gia_tri',
                'promos.don_vi_tinh as km_don_vi_tinh'
            ]);
    }

    public function getViewHome()
    {
        $baseQuery = $this->buildBaseProductQuery();

        $products = (clone $baseQuery)
            ->orderBy('san_pham.created_at', 'desc')
            ->limit(10)
            ->get();

        $rackets = (clone $baseQuery)
            ->where('danh_muc.slug', 'vot-cau-long')
            ->orderBy('san_pham.created_at', 'desc')
            ->limit(10)
            ->get();

        $popular = (clone $baseQuery)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $shoes = (clone $baseQuery)
            ->where('danh_muc.slug', 'giay-cau-long')
            ->orderBy('san_pham.created_at', 'desc')
            ->limit(10)
            ->get();

        $banner = DB::table('banner')
            ->select(['id_banner', 'img_url', 'thu_tu', 'href'])
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
