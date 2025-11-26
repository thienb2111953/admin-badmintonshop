<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SanPhamController extends Controller
{
    public function dsSanPhamChiTiet(Request $request)
    {
        $query = DB::table('san_pham')
            ->leftJoin('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham', '=', 'san_pham.id_san_pham')
            ->leftJoin('anh_san_pham', 'anh_san_pham.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->leftJoin('mau', 'mau.id_mau', '=', 'san_pham_chi_tiet.id_mau')
            ->leftJoin('kich_thuoc', 'kich_thuoc.id_kich_thuoc', '=', 'san_pham_chi_tiet.id_kich_thuoc')
            ->leftJoin('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->leftJoin('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->leftJoin('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->leftJoin('thuoc_tinh','thuoc_tinh.id_thuoc_tinh','=','danh_muc.id_thuoc_tinh')
            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham_chi_tiet.gia_ban',
                'san_pham_chi_tiet.so_luong_ton',
                'san_pham.trang_thai',
                'thuong_hieu.ten_thuong_hieu',
                'mau.ten_mau',
                'kich_thuoc.ten_kich_thuoc',
                'anh_san_pham.anh_url',
                'anh_san_pham.thu_tu',
                'san_pham_chi_tiet.id_san_pham_chi_tiet'
            ])
            ->orderBy('san_pham.id_san_pham')
            ->orderBy('san_pham_chi_tiet.ten_san_pham_chi_tiet')
            ->get();


        return Response::Success($query, '');
    }

    public function getRelatedProduct($categorySlug, $categoryBrandSlug)
    {
        return DB::table('san_pham as sp')
            ->select('sp.*')
            ->addSelect(['anh_url' => function ($q) {
                $q->selectRaw("CONCAT('" . env('APP_URL') . "/storage/', asp.anh_url)")
                    ->from('san_pham_chi_tiet as spct')
                    ->join('anh_san_pham as asp', 'spct.id_san_pham_chi_tiet', '=', 'asp.id_san_pham_chi_tiet')
                    ->whereColumn('spct.id_san_pham', 'sp.id_san_pham')
                    ->where('asp.thu_tu', 1)
                    ->orderBy('spct.id_san_pham_chi_tiet', 'asc')
                    ->limit(1);
            }])
            ->addSelect(['gia_thap_nhat' => function ($q) {
                $q->selectRaw('MIN(gia_ban)')
                    ->from('san_pham_chi_tiet')
                    ->whereColumn('id_san_pham', 'sp.id_san_pham');
            }])
            ->addSelect(['gia_cao_nhat' => function ($q) {
                $q->selectRaw('MAX(gia_ban)')
                    ->from('san_pham_chi_tiet')
                    ->whereColumn('id_san_pham', 'sp.id_san_pham');
            }])
            ->join('danh_muc_thuong_hieu as dmth', 'sp.id_danh_muc_thuong_hieu', '=', 'dmth.id_danh_muc_thuong_hieu')
            ->join('thuong_hieu as th', 'dmth.id_thuong_hieu', '=', 'th.id_thuong_hieu')
            ->join('danh_muc as dm', 'dmth.id_danh_muc', '=', 'dm.id_danh_muc')
            ->where('dm.slug', $categorySlug)
            ->where('dmth.slug', $categoryBrandSlug)
            ->limit(8)
            ->orderBy('sp.created_at', 'desc')
            ->get();
    }

    public function getProductsDetail(Request $request, $param)
    {
        $now = now();

        $query = DB::table('san_pham')
            ->leftJoin('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham', '=', 'san_pham.id_san_pham')
            ->leftJoin('anh_san_pham', 'anh_san_pham.id_san_pham_chi_tiet', '=', 'san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->leftJoin('mau', 'mau.id_mau', '=', 'san_pham_chi_tiet.id_mau')
            ->leftJoin('kich_thuoc', 'kich_thuoc.id_kich_thuoc', '=', 'san_pham_chi_tiet.id_kich_thuoc')
            ->leftJoin('danh_muc_thuong_hieu', 'danh_muc_thuong_hieu.id_danh_muc_thuong_hieu', '=', 'san_pham.id_danh_muc_thuong_hieu')
            ->leftJoin('thuong_hieu', 'thuong_hieu.id_thuong_hieu', '=', 'danh_muc_thuong_hieu.id_thuong_hieu')
            ->leftJoin('danh_muc', 'danh_muc.id_danh_muc', '=', 'danh_muc_thuong_hieu.id_danh_muc')
            ->leftJoin('san_pham_thuoc_tinh', 'san_pham_thuoc_tinh.id_san_pham', '=', 'san_pham.id_san_pham')
            ->leftJoin('thuoc_tinh_chi_tiet', 'thuoc_tinh_chi_tiet.id_thuoc_tinh_chi_tiet', '=', 'san_pham_thuoc_tinh.id_thuoc_tinh_chi_tiet')
            ->leftJoin('thuoc_tinh', 'thuoc_tinh.id_thuoc_tinh', '=', 'thuoc_tinh_chi_tiet.id_thuoc_tinh')
            ->leftJoin('san_pham_khuyen_mai', 'san_pham_khuyen_mai.id_san_pham', '=', 'san_pham.id_san_pham')
            ->leftJoin('khuyen_mai', function ($join) use ($now) {
                $join->on('khuyen_mai.id_khuyen_mai', '=', 'san_pham_khuyen_mai.id_khuyen_mai')
                    ->where('khuyen_mai.ngay_bat_dau', '<=', $now)
                    ->where('khuyen_mai.ngay_ket_thuc', '>=', $now);
            })

            ->select([
                'san_pham.id_san_pham',
                'san_pham.ma_san_pham',
                'san_pham.ten_san_pham',
                'san_pham.mo_ta',
                'san_pham.trang_thai',
                'thuong_hieu.ten_thuong_hieu',

                // Chi tiết biến thể
                'san_pham_chi_tiet.id_san_pham_chi_tiet',
                'san_pham_chi_tiet.gia_niem_yet',
                'san_pham_chi_tiet.gia_ban',
                'san_pham_chi_tiet.so_luong_ton',
                // Màu & Size
                'mau.id_mau',
                'mau.ten_mau',
                'kich_thuoc.id_kich_thuoc',
                'kich_thuoc.ten_kich_thuoc',
                // Ảnh
                'anh_san_pham.anh_url',
                'anh_san_pham.thu_tu',
                // Thông số
                'thuoc_tinh.ten_thuoc_tinh',
                'thuoc_tinh_chi_tiet.ten_thuoc_tinh_chi_tiet',

                // --- Thông tin khuyến mãi ---
                'khuyen_mai.gia_tri as km_gia_tri',
                'khuyen_mai.don_vi_tinh as km_don_vi_tinh',

                'danh_muc.slug',
                'danh_muc_thuong_hieu.slug as slug_danh_muc_thuong_hieu'
            ])
            ->where('san_pham.slug', $param)
            ->whereNotNull('san_pham_chi_tiet.id_san_pham_chi_tiet')
            ->get();

        if ($query->isEmpty()) {
            return Response::Error('Product not found', 404);
        }

        $categorySlug = $query->first()->slug;
        $categoryBrandSlug = $query->first()->slug_danh_muc_thuong_hieu;
        $relatedProduct = $this->getRelatedProduct($categorySlug, $categoryBrandSlug);

        $result = $query->groupBy('ma_san_pham')->map(function ($items) use ($relatedProduct) {
            $first = $items->first();

            $mau_options = $items
                ->filter(fn($item) => !is_null($item->id_mau))
                ->unique('id_mau')
                ->map(fn($item) => [
                    'id_mau' => $item->id_mau,
                    'ten_mau' => $item->ten_mau,
                ])
                ->values();

            $kich_thuoc_options = $items
                ->filter(fn($item) => !is_null($item->id_kich_thuoc))
                ->unique('id_kich_thuoc')
                ->map(fn($item) => [
                    'id_kich_thuoc' => $item->id_kich_thuoc,
                    'ten_kich_thuoc' => $item->ten_kich_thuoc,
                ])
                ->values();

            $specifications = $items
                ->filter(fn($item) => !is_null($item->ten_thuoc_tinh) && !is_null($item->ten_thuoc_tinh_chi_tiet))
                ->unique(function ($item) {
                    return $item->ten_thuoc_tinh . '-' . $item->ten_thuoc_tinh_chi_tiet;
                })
                ->map(fn($item) => [
                    'name' => $item->ten_thuoc_tinh,
                    'value' => $item->ten_thuoc_tinh_chi_tiet,
                ])
                ->values();

            $variants = $items
                ->groupBy('id_san_pham_chi_tiet')
                ->map(function ($variantItems) {
                    $firstVariant = $variantItems->first();

                    $anh_san_pham = $variantItems
                        ->filter(fn($item) => !is_null($item->anh_url))
                        ->unique('anh_url')
                        ->sortBy('thu_tu')
                        ->map(function ($imageItem) {
                            return [
                                'anh_url' => env('APP_URL') . '/storage/' . $imageItem->anh_url,
                                'thu_tu' => $imageItem->thu_tu,
                            ];
                        })
                        ->values();

                    return [
                        'id_san_pham_chi_tiet' => $firstVariant->id_san_pham_chi_tiet,
                        'id_mau' => $firstVariant->id_mau,
                        'id_kich_thuoc' => $firstVariant->id_kich_thuoc,
                        'so_luong_ton' => $firstVariant->so_luong_ton,
                        'gia_niem_yet' => $firstVariant->gia_niem_yet,
                        'gia_ban' => $firstVariant->gia_ban,
                        'anh' => $anh_san_pham,
                    ];
                })
                ->values();

            return [
                'id_san_pham' => $first->id_san_pham,
                'ma_san_pham' => $first->ma_san_pham,
                'ten_san_pham' => $first->ten_san_pham,
                'mo_ta' => $first->mo_ta,
                'trang_thai' => $first->trang_thai,
                'ten_thuong_hieu' => $first->ten_thuong_hieu,
                'km_gia_tri' => $first->km_gia_tri,
                'km_don_vi_tinh' => $first->km_don_vi_tinh,
                'thong_so_ky_thuat' => $specifications,
                'options' => [
                    'mau' => $mau_options,
                    'kich_thuoc' => $kich_thuoc_options,
                ],
                'variants' => $variants,
                'related_products' => $relatedProduct,
            ];
        })->first();

        return Response::Success($result, '');
    }

    public function productSearch(Request $request)
    {
        $searchString = $request->input('keyword', '');

        $query = DB::table('san_pham')
            ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham', '=', 'san_pham.id_san_pham')
            ->when($searchString, function ($query, $searchString) {
                return $query->where('ten_san_pham', 'ilike', '%' . $searchString . '%');
            })
            ->get();

        if(!$query->isEmpty()) {
            return Response::Success($query, 'Response search successfully');
        }

        return Response::Success([], 'Response search successfully');
    }
}
