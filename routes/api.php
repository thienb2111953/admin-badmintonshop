<?php

use App\Http\Controllers\Admin\NguoiDungController;
use App\Http\Controllers\Admin\QuyenController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TrangChuController;
use App\Http\Controllers\ChatBotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\SanPhamController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ThuongHieuController;

Route::post('/chatbot', [ChatbotController::class, 'reply']);

Route::get('quyen', [QuyenController::class, 'dsQuyen'])->name('QuyenController.dsQuyen');
Route::post('quyen', [QuyenController::class, 'them'])->name('QuyenController.them');

Route::get('trang-chu', [TrangChuController::class, 'getViewHome'])->name('TrangChuController.getViewHome');

Route::group(['prefix' => 'danh-muc'], function () {
    Route::get('/', [DanhMucController::class, 'getDanhMuc'])->name('DanhMucController.getDanhMuc');
    Route::get('/{param}', [DanhMucController::class, 'getProductByCategory'])->name('DanhMucController.getProductByCategory');
    Route::get('/{categorySlug}/{categoryBrandSlug}', [DanhMucController::class, 'getProductByCategoryBrand']);
});


Route::group(['prefix' => 'san-pham'], function () {
    Route::get('/ds', [SanPhamController::class, 'dsSanPhamChiTiet'])->name('SanPhamController.dsSanPhamChiTiet');

    Route::get('/{param}', [SanPhamController::class, 'getProductsDetail'])->name('SanPhamController.getProductsDetail');
    Route::get('/search', [SanPhamController::class, 'productSearch'])->name('SanPhamController.productSearch');
});

Route::get('/thuong-hieu', [ThuongHieuController::class, 'getAllThuongHieu'])->name('ThuongHieuController.getAllThuongHieu');


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('jwt')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']);

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'cart']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/remove', [CartController::class, 'removeFromCart']);
        Route::post('/update', [CartController::class, 'updateQuantity']);
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [UserAddressController::class, 'index']);
        Route::post('/', [UserAddressController::class, 'store']);
        Route::put('/{id}', [UserAddressController::class, 'edit']);
        Route::delete('/{id}', [UserAddressController::class, 'destroy']);
        Route::get('/default', [UserAddressController::class, 'getDefaultAddress']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::get('/{id_don_hang}', [OrderController::class, 'getOrderDetail']);
    });
});

Route::post('tao-don-hang', [CheckOutController::class, 'taoDonHang'])->name('CheckOutController.taoDonHang');
Route::post('check-out', [CheckOutController::class, 'vnpayPayment'])->name('CheckOutController.vnpayPayment');
Route::get('vnpay-return', [CheckOutController::class, 'vnpayReturn'])->name('CheckOutController.vnpayReturn');

