<?php

use App\Http\Controllers\Admin\AnhSanPhamController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CaiDatController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\DanhMucThuongHieuController;
use App\Http\Controllers\Admin\DonHangChiTietController;
use App\Http\Controllers\Admin\DonHangController;
use App\Http\Controllers\Admin\DonHangKhuyenMaiController;
use App\Http\Controllers\Admin\KhuyenMaiController;
use App\Http\Controllers\Admin\KichThuocController;
use App\Http\Controllers\Admin\MauController;
use App\Http\Controllers\Admin\NguoiDungController;
use App\Http\Controllers\Admin\NhapHangChiTietController;
use App\Http\Controllers\Admin\NhapHangController;
use App\Http\Controllers\Admin\SanPhamChiTietController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\SanPhamKhuyenMaiController;
use App\Http\Controllers\Admin\ThanhToanController;
use App\Http\Controllers\Admin\ThongKeController;
use App\Http\Controllers\Admin\ThuocTinhChiTietController;
use App\Http\Controllers\Admin\ThuocTinhController;
use App\Http\Controllers\Admin\ThuongHieuController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::prefix('nguoi-dung')->group(function () {
        Route::get('/', [NguoiDungController::class, 'index'])->name('nguoi_dung');
        Route::post('/', [NguoiDungController::class, 'store'])->name('nguoi_dung.store');
        Route::put('/', [NguoiDungController::class, 'update'])->name('nguoi_dung.update');
        Route::delete('/', [NguoiDungController::class, 'destroy'])->name('nguoi_dung.destroy');
    });

    Route::prefix('thuong-hieu')->group(function () {
        Route::get('/', [ThuongHieuController::class, 'index'])->name('thuong_hieu');
        Route::post('/', [ThuongHieuController::class, 'store'])->name('thuong_hieu.store');
        Route::put('/', [ThuongHieuController::class, 'update'])->name('thuong_hieu.update');
        Route::delete('/', [ThuongHieuController::class, 'destroy'])->name('thuong_hieu.destroy');
    });

    Route::prefix('danh-muc')->group(function () {
        Route::get('/', [DanhMucController::class, 'index'])->name('danh_muc');
        Route::post('/', [DanhMucController::class, 'store'])->name('danh_muc.store');
        Route::put('/', [DanhMucController::class, 'update'])->name('danh_muc.update');
        Route::delete('/', [DanhMucController::class, 'destroy'])->name('danh_muc.destroy');
    });

    Route::prefix('mau')->group(function () {
        Route::get('/', [MauController::class, 'index'])->name('mau');
        Route::post('/', [MauController::class, 'store'])->name('mau.store');
        Route::put('/', [MauController::class, 'update'])->name('mau.update');
        Route::delete('/', [MauController::class, 'destroy'])->name('mau.destroy');
    });

    Route::prefix('kich-thuoc')->group(function () {
        Route::get('/', [KichThuocController::class, 'index'])->name('kich_thuoc');
        Route::post('/', [KichThuocController::class, 'store'])->name('kich_thuoc.store');
        Route::put('/', [KichThuocController::class, 'update'])->name('kich_thuoc.update');
        Route::delete('/', [KichThuocController::class, 'destroy'])->name('kich_thuoc.destroy');
    });

    Route::prefix('thuoc-tinh')->group(function () {
        Route::get('/', [ThuocTinhController::class, 'index'])->name('thuoc_tinh');
        Route::post('/', [ThuocTinhController::class, 'store'])->name('thuoc_tinh.store');
        Route::put('/', [ThuocTinhController::class, 'update'])->name('thuoc_tinh.update');
        Route::delete('/', [ThuocTinhController::class, 'destroy'])->name('thuoc_tinh.destroy');

        Route::prefix('{id_thuoc_tinh}')->group(function () {
            Route::get('/', [ThuocTinhChiTietController::class, 'index'])->name('thuoc_tinh_chi_tiet');
            Route::post('/', [ThuocTinhChiTietController::class, 'store'])->name('thuoc_tinh_chi_tiet.store');
            Route::put('/', [ThuocTinhChiTietController::class, 'update'])->name('thuoc_tinh_chi_tiet.update');
            Route::delete('/', [ThuocTinhChiTietController::class, 'destroy'])->name('thuoc_tinh_chi_tiet.destroy');
        });
    });

    Route::prefix('san-pham-thuong-hieu')->group(function () {
        Route::get('/', [DanhMucThuongHieuController::class, 'index'])->name('san_pham_thuong_hieu');
        Route::get('/them', [DanhMucThuongHieuController::class, 'storeView'])->name('danh_muc_thuong_hieu.storeView');
        Route::get('/edit/{id_danh_muc_thuong_hieu}', [DanhMucThuongHieuController::class, 'updateView'])->name(
            'danh_muc_thuong_hieu.updateView',
        );
        Route::post('/', [DanhMucThuongHieuController::class, 'store'])->name('danh_muc_thuong_hieu.store');
        Route::put('/', [DanhMucThuongHieuController::class, 'update'])->name('danh_muc_thuong_hieu.update');
        Route::delete('/', [DanhMucThuongHieuController::class, 'destroy'])->name('danh_muc_thuong_hieu.destroy');

        Route::post('/export-san-pham', [DanhMucThuongHieuController::class, 'exportSanPham'])->name('danh_muc_thuong_hieu.export');

        Route::prefix('{id_danh_muc_thuong_hieu}/san-pham')->group(function () {
            Route::get('/', [SanPhamController::class, 'index'])->name('san_pham');
            Route::get('/them', [SanPhamController::class, 'storeView'])->name('san_pham.storeView');
            Route::get('/edit/{id_san_pham}', [SanPhamController::class, 'updateView'])->name('san_pham.updateView');

            Route::post('/', [SanPhamController::class, 'store'])->name('san_pham.store');
            Route::put('/', [SanPhamController::class, 'update'])->name('san_pham.update');
            Route::delete('/', [SanPhamController::class, 'destroy'])->name('san_pham.destroy');
        });
    });

    Route::prefix('san-pham/{id_san_pham}')->group(function () {
        Route::get('/', [SanPhamChiTietController::class, 'index'])->name('san_pham_chi_tiet');
        Route::post('/', [SanPhamChiTietController::class, 'store'])->name('san_pham_chi_tiet.store');
        Route::put('/', [SanPhamChiTietController::class, 'update'])->name('san_pham_chi_tiet.update');
        Route::delete('/', [SanPhamChiTietController::class, 'destroy'])->name('san_pham_chi_tiet.destroy');

        Route::prefix('anh-san-pham')->group(function () {
            Route::post('/', [AnhSanPhamController::class, 'store'])->name('anh_san_pham.store');
            Route::put('/', [AnhSanPhamController::class, 'update'])->name('anh_san_pham.update');
            Route::delete('/', [AnhSanPhamController::class, 'destroy'])->name('anh_san_pham.destroy');
        });
    });

    Route::prefix('cai-dat')->group(function () {
        Route::get('/', [CaiDatController::class, 'index'])->name('cai_dat');
        Route::post('/', [CaiDatController::class, 'store'])->name('cai_dat.store');
        Route::put('/', [CaiDatController::class, 'update'])->name('cai_dat.update');
        Route::delete('/', [CaiDatController::class, 'destroy'])->name('cai_dat.destroy');
    });

    Route::prefix('banner')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('banner');
        Route::post('/', [BannerController::class, 'store'])->name('banner.store');
        Route::put('/', [BannerController::class, 'update'])->name('banner.update');
        Route::delete('/', [BannerController::class, 'destroy'])->name('banner.destroy');
    });

    Route::prefix('nhap-hang')->group(function () {
        Route::get('', [NhapHangController::class, 'index'])->name('nhap_hang');
        Route::post('', [NhapHangController::class, 'store'])->name('nhap_hang.store');
        Route::put('', [NhapHangController::class, 'update'])->name('nhap_hang.update');
        Route::delete('', [NhapHangController::class, 'destroy'])->name('nhap_hang.destroy');
        Route::prefix('{id_nhap_hang}')->group(function () {
            Route::get('/', [NhapHangChiTietController::class, 'index'])->name('nhap_hang_chi_tiet');
            Route::post('/', [NhapHangChiTietController::class, 'store'])->name('nhap_hang_chi_tiet.store');
            Route::put('/', [NhapHangChiTietController::class, 'update'])->name('nhap_hang_chi_tiet.update');
            Route::delete('/', [NhapHangChiTietController::class, 'destroy'])->name('nhap_hang_chi_tiet.destroy');
        });
    });

    Route::prefix('thanh-toan')->group(function () {
        Route::get('', [ThanhToanController::class, 'index'])->name('thanh_toan');
    });

    Route::prefix('don-hang')->group(function () {
        Route::get('', [DonHangController::class, 'index'])->name('don_hang');
        Route::patch('', [DonHangController::class, 'updateTrangThai'])->name('don_hang.updateTrangThai');
        // Route::post('', [DonHangController::class, 'store'])->name('don_hang.store');
        // Route::put('', [DonHangController::class, 'update'])->name('don_hang.update');
        // Route::delete('', [DonHangController::class, 'destroy'])->name('don_hang.destroy');
        // Route::prefix('{id_nhap_hang}')->group(function () {
        Route::get('{id_don_hang}', [DonHangChiTietController::class, 'index'])->name('don_hang_chi_tiet');// routes/api.php
        Route::get('{id_don_hang}/print', [DonHangChiTietController::class, 'inHoaDon']);

        // });
    });

    Route::prefix('thong-ke')->group(function () {
        Route::get('', [ThongKeController::class, 'index'])->name('thong_ke');
        Route::get('/doanh-thu', [ThongKeController::class, 'doanhThu'])->name('thong_ke.doanh_thu');
        Route::get('/san-pham', [ThongKeController::class, 'thongKeSanPham'])->name('thong_ke.san_pham');
    });

    Route::prefix('khuyen-mai')->group(function () {
        Route::get('/', [KhuyenMaiController::class, 'index'])->name('khuyen_mai');
        Route::post('/', [KhuyenMaiController::class, 'store'])->name('khuyen_mai.store');
        Route::put('/', [KhuyenMaiController::class, 'update'])->name('khuyen_mai.update');
        Route::delete('/', [KhuyenMaiController::class, 'destroy'])->name('khuyen_mai.destroy');
    });

    Route::prefix('san-pham-khuyen-mai')->group(function () {
        Route::post('/', [SanPhamKhuyenMaiController::class, 'store'])->name('san_pham_khuyen_mai.store');
        Route::put('/', [SanPhamKhuyenMaiController::class, 'update'])->name('san_pham_khuyen_mai.update');
        Route::delete('/', [SanPhamKhuyenMaiController::class, 'destroy'])->name('san_pham_khuyen_mai.destroy');
    });

    Route::prefix('don-hang-khuyen-mai')->group(function () {
        Route::post('/', [DonHangKhuyenMaiController::class, 'store'])->name('don_hang_khuyen_mai.store');
        Route::put('/', [DonHangKhuyenMaiController::class, 'update'])->name('don_hang_khuyen_mai.update');
        Route::delete('/', [DonHangKhuyenMaiController::class, 'destroy'])->name('don_hang_khuyen_mai.destroy');
    });



});

//    Route::get('vnpay-return', [CheckOutController::class, 'vnpayReturn'])->name('CheckOutController.vnpayReturn');
//    Route::post('check-out', [CheckOutController::class, 'vnpayPayment'])->name('CheckOutController.vnpayPayment');



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
