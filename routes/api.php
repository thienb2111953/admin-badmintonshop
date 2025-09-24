<?php

use App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('quyen', [QuyenController::class, 'dsQuyen'])->name('QuyenController.dsQuyen');
Route::post('quyen', [QuyenController::class, 'them'])->name('QuyenController.them');

Route::get('san-pham', [ApiController::class, 'dsSanPham']);
// ->withoutMiddleware('auth:sanctum');
