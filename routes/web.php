<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Quyen;
use \App\Http\Controllers\Admin\QuyenController;

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
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
