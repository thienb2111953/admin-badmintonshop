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

    public function getProductByCategory(Request $request, $slug)
    {
        $now = now();

        $query = DB::table('san_pham as sp')
            ->select('sp.*')
            ->addSelect([
                'km_gia_tri' => 'km.gia_tri as km_gia_tri',
                'km_don_vi_tinh' => 'km.don_vi_tinh as km_don_vi_tinh'
            ])
            ->addSelect(['anh_url' => function ($q) {
                $q->select('asp.anh_url')
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
            ->leftJoin('san_pham_khuyen_mai as spkm', 'spkm.id_san_pham', '=', 'sp.id_san_pham')
            ->leftJoin('khuyen_mai as km', function ($join) use ($now) {
                $join->on('km.id_khuyen_mai', '=', 'spkm.id_khuyen_mai')
                    ->where('km.ngay_bat_dau', '<=', $now)
                    ->where('km.ngay_ket_thuc', '>=', $now);
            })
            ->where('dm.slug', $slug);

        if ($request->has('brands') && is_array($request->brands) && count($request->brands) > 0) {
            $query->whereIn('th.ten_thuong_hieu', $request->brands);
        }

        if ($request->has('price_ranges') && is_array($request->price_ranges) && count($request->price_ranges) > 0) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price_ranges as $range) {
                    $q->orWhereExists(function ($subQuery) use ($range) {
                        $subQuery->select(DB::raw(1))
                            ->from('san_pham_chi_tiet as spct_filter')
                            ->whereColumn('spct_filter.id_san_pham', 'sp.id_san_pham');

                        switch ($range) {
                            case 'range_1': // Dưới 500k
                                $subQuery->where('gia_ban', '<', 500000);
                                break;
                            case 'range_2': // 500k - 1tr
                                $subQuery->whereBetween('gia_ban', [500000, 1000000]);
                                break;
                            case 'range_3': // 1tr - 2tr
                                $subQuery->whereBetween('gia_ban', [1000000, 2000000]);
                                break;
                            case 'range_4': // Trên 2tr
                                $subQuery->where('gia_ban', '>', 2000000);
                                break;
                        }
                    });
                }
            });
        }

        $inputAttributes = $request->input('attributes');

        if ($inputAttributes && is_array($inputAttributes) && count($inputAttributes) > 0) {

            $selectedAttrCount = count($inputAttributes);

            $query->whereExists(function ($subQuery) use ($inputAttributes, $selectedAttrCount) {
                $subQuery->select(DB::raw(1))
                    ->from('san_pham_thuoc_tinh as sptt')
                    ->whereColumn('sptt.id_san_pham', 'sp.id_san_pham')
                    ->whereIn('sptt.id_thuoc_tinh_chi_tiet', $inputAttributes)
                    ->groupBy('sptt.id_san_pham')
                    ->havingRaw('COUNT(DISTINCT sptt.id_thuoc_tinh_chi_tiet) = ?', [$selectedAttrCount]);
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'low_to_hight':
                case 'low_to_high':
                    $query->orderBy('gia_thap_nhat', 'asc');
                    break;
                case 'hight_to_low':
                case 'high_to_low':
                    $query->orderBy('gia_thap_nhat', 'desc');
                    break;
                default:
                    $query->orderBy('sp.created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('sp.created_at', 'desc');
        }

        $products = $query->paginate(12);

        return Response::Success($products, 'Lấy danh sách sản phẩm thành công');
    }

    public function getProductByCategoryBrand(Request $request, $categorySlug, $categoryBrandSlug)
    {
        $now = now();

        $query = DB::table('san_pham as sp')
            ->select('sp.*')
            ->addSelect([
                'km_gia_tri' => 'km.gia_tri as km_gia_tri',
                'km_don_vi_tinh' => 'km.don_vi_tinh as km_don_vi_tinh',
                'th.ten_thuong_hieu'
            ])
            ->addSelect(['anh_url' => function ($q) {
                $q->select('asp.anh_url')
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
            ->leftJoin('san_pham_khuyen_mai as spkm', 'spkm.id_san_pham', '=', 'sp.id_san_pham')
            ->leftJoin('khuyen_mai as km', function ($join) use ($now) {
                $join->on('km.id_khuyen_mai', '=', 'spkm.id_khuyen_mai')
                    ->where('km.ngay_bat_dau', '<=', $now)
                    ->where('km.ngay_ket_thuc', '>=', $now);
            })
            ->where('dm.slug', $categorySlug)
            ->where('dmth.slug', $categoryBrandSlug);

        if ($request->has('brands') && is_array($request->brands) && count($request->brands) > 0) {
            $query->whereIn('th.ten_thuong_hieu', $request->brands);
        }

        $inputAttributes = $request->input('attributes');

        if ($inputAttributes && is_array($inputAttributes) && count($inputAttributes) > 0) {

            $selectedAttrCount = count($inputAttributes);

            $query->whereExists(function ($subQuery) use ($inputAttributes, $selectedAttrCount) {
                $subQuery->select(DB::raw(1))
                    ->from('san_pham_thuoc_tinh as sptt')
                    ->whereColumn('sptt.id_san_pham', 'sp.id_san_pham')
                    ->whereIn('sptt.id_thuoc_tinh_chi_tiet', $inputAttributes)
                    ->groupBy('sptt.id_san_pham')
                    ->havingRaw('COUNT(DISTINCT sptt.id_thuoc_tinh_chi_tiet) = ?', [$selectedAttrCount]);
            });
        }

        if ($request->has('price_ranges') && is_array($request->price_ranges) && count($request->price_ranges) > 0) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price_ranges as $range) {
                    $q->orWhereExists(function ($subQuery) use ($range) {
                        $subQuery->select(DB::raw(1))
                            ->from('san_pham_chi_tiet as spct_filter')
                            ->whereColumn('spct_filter.id_san_pham', 'sp.id_san_pham');

                        switch ($range) {
                            case 'range_1': // Dưới 500k
                                $subQuery->where('gia_ban', '<', 500000);
                                break;
                            case 'range_2': // 500k - 1tr
                                $subQuery->whereBetween('gia_ban', [500000, 1000000]);
                                break;
                            case 'range_3': // 1tr - 2tr
                                $subQuery->whereBetween('gia_ban', [1000000, 2000000]);
                                break;
                            case 'range_4': // Trên 2tr
                                $subQuery->where('gia_ban', '>', 2000000);
                                break;
                        }
                    });
                }
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'low_to_hight':
                case 'low_to_high':
                    $query->orderBy('gia_thap_nhat', 'asc');
                    break;
                case 'hight_to_low':
                case 'high_to_low':
                    $query->orderBy('gia_thap_nhat', 'desc');
                    break;
                default:
                    $query->orderBy('sp.created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('sp.created_at', 'desc');
        }

        $products = $query->paginate(12);

        return Response::Success($products, 'Lấy danh sách sản phẩm thành công');
    }
}
