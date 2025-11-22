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
        $now = now(); // Lấy thời gian hiện tại

        // 1. Subquery lấy biến thể (Giữ nguyên logic của bạn)
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

        // 2. [MỚI] Subquery lấy Khuyến mãi hợp lệ
        // Logic: Chỉ lấy khuyến mãi đang trong thời gian diễn ra (ngay_bat_dau <= now <= ngay_ket_thuc)
        $promoSubquery = DB::table('san_pham_khuyen_mai as spkm')
            ->join('khuyen_mai as km', 'km.id_khuyen_mai', '=', 'spkm.id_khuyen_mai')
            ->select(
                'spkm.id_san_pham',
                'km.ten_khuyen_mai',
                'km.gia_tri',       // Giá trị giảm (vd: 10 hoặc 50000)
                'km.don_vi_tinh',   // Đơn vị (vd: '%' hoặc 'VND')
                // Nếu 1 sản phẩm có nhiều khuyến mãi chồng chéo, ưu tiên cái có giá trị cao nhất
                DB::raw('ROW_NUMBER() OVER(PARTITION BY spkm.id_san_pham ORDER BY km.gia_tri DESC) as rn_promo')
            )
            ->where('km.ngay_bat_dau', '<=', $now)
            ->where('km.ngay_ket_thuc', '>=', $now);

        // 3. Query chính
        $baseQuery = DB::table('san_pham')
            ->join('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->join('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->join('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')

            // Join biến thể (như cũ)
            ->joinSub($variantSubquery, 'variants', function ($join) {
                $join->on('san_pham.id_san_pham', '=', 'variants.id_san_pham')
                    ->where('variants.rn', '=', 1);
            })

            // [MỚI] Left Join khuyến mãi (Dùng Left Join vì có sp không có khuyến mãi)
            ->leftJoinSub($promoSubquery, 'promos', function ($join) {
                $join->on('san_pham.id_san_pham', '=', 'promos.id_san_pham')
                    ->where('promos.rn_promo', '=', 1); // Chỉ lấy khuyến mãi ưu tiên số 1
            })

            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.slug',
                'variants.gia_niem_yet', // Giá gạch ngang gốc
                'variants.gia_ban',      // Giá bán thường
                'variants.gia_thap_nhat',
                'variants.gia_cao_nhat',
                'variants.anh_url',

                // [MỚI] Các cột khuyến mãi
                'promos.ten_khuyen_mai',
                'promos.gia_tri as km_gia_tri',
                'promos.don_vi_tinh as km_don_vi_tinh'
            ])
            ->orderBy('san_pham.created_at', 'desc');

        // Thực hiện query (Clone để tái sử dụng query builder)
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
