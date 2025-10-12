<?php

use App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Api\TrangChuController;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DanhMucController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('quyen', [QuyenController::class, 'dsQuyen'])->name('QuyenController.dsQuyen');
Route::post('quyen', [QuyenController::class, 'them'])->name('QuyenController.them');


Route::group(['prefix' => 'danh-muc'], function () {
    Route::get('/', [DanhMucController::class, 'getDanhMuc'])->name('DanhMucController.getDanhMuc');
});

Route::get('trang-chu', [TrangChuController::class, 'getViewHome'])->name('TrangChuController.getViewHome');
