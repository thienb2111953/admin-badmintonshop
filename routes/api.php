<?php

use App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Api\TrangChuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\SanPhamController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('quyen', [QuyenController::class, 'dsQuyen'])->name('QuyenController.dsQuyen');
Route::post('quyen', [QuyenController::class, 'them'])->name('QuyenController.them');

Route::group(['prefix' => 'danh-muc'], function () {
    Route::get('/', [DanhMucController::class, 'getDanhMuc'])->name('DanhMucController.getDanhMuc');
});

Route::get('trang-chu', [TrangChuController::class, 'getViewHome'])->name('TrangChuController.getViewHome');
Route::group(['prefix' => 'san-pham'], function () {
    Route::get('/{param}', [SanPhamController::class, 'getProductsDetail'])->name('SanPhamController.getProductsDetail');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('vnpay-return', [CheckOutController::class, 'vnpayReturn'])->name('CheckOutController.vnpayReturn');
    Route::post('check-out', [CheckOutController::class, 'vnpayPayment'])->name('CheckOutController.vnpayPayment');
});

