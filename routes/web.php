<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Quyen;


Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Route::get('quyen', function () {
    //     return Inertia::render('admin/quyen', [
    //         'initialData' => Quyen::all()
    //     ]);
    // })->name('quyen.index');

    Route::get('quyen', [\App\Http\Controllers\Admin\QuyenController::class, 'index'])->name('quyen.index');
    Route::get('dsQuyen', [\App\Http\Controllers\Admin\QuyenController::class, 'dsQuyen']);
    Route::post('quyen/store-or-update', [\App\Http\Controllers\Admin\QuyenController::class, 'storeOrUpdate'])->name('quyen.storeOrUpdate');
    Route::delete('quyen', [\App\Http\Controllers\Admin\QuyenController::class, 'destroy'])->name('quyen.destroy');
    Route::delete('xoa-nhieu-quyen', [\App\Http\Controllers\Admin\QuyenController::class, 'destroyMultiple'])->name('quyen.destroyMultiple');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
