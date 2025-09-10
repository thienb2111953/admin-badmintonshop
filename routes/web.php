<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Quyen;
use \App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Admin\NguoiDungController;
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
        Route::patch('/', [ThuongHieuController::class, 'update'])->name('thuong_hieu.update');
        Route::delete('/', [ThuongHieuController::class, 'destroy'])->name('thuong_hieu.destroy');
    });

    Route::prefix('danh-muc')->group(function () {
        Route::get('/', [ThuongHieuController::class, 'index'])->name('danh_muc');
        Route::post('/', [ThuongHieuController::class, 'store'])->name('danh_muc.store');
        Route::patch('/', [ThuongHieuController::class, 'update'])->name('danh_muc.update');
        Route::delete('/', [ThuongHieuController::class, 'destroy'])->name('danh_muc.destroy');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
