<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DonHangController extends Controller
{
    public function index()
    {
        $donHangs = DB::table('don_hang')
            ->join('nguoi_dung', 'don_hang.id_nguoi_dung', '=', 'nguoi_dung.id_nguoi_dung')
            ->select(
                'don_hang.*',
                DB::raw("CONCAT(nguoi_dung.name, ' (', nguoi_dung.email, ')') as nguoi_dung_thong_tin")
            )
            ->get();

        return Inertia::render('admin/don-hang/don-hang', [
            'don_hangs' => $donHangs
        ]);
    }
}
