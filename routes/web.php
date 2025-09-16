<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DanhMucController;
use \App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Admin\NguoiDungController;
use App\Http\Controllers\Admin\ThuocTinhChiTietController;
use App\Http\Controllers\Admin\ThuocTinhController;
use App\Http\Controllers\Admin\ThuongHieuController;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::prefix('quyen')->group(function () {
        Route::get('/', [QuyenController::class, 'index'])->name('quyen');
        Route::post('/', [QuyenController::class, 'store'])->name('quyen.store');
        Route::put('/', [QuyenController::class, 'update'])->name('quyen.update');
        Route::delete('/', [QuyenController::class, 'destroy'])->name('quyen.destroy');
        Route::delete('xoa-nhieu', [QuyenController::class, 'destroyMultiple'])->name('quyen.destroyMultiple');
        Route::get('ds-quyen', [QuyenController::class, 'dsQuyen'])->name('quyen.dsQuyen');
    });

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
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
