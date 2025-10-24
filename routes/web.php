<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarController;
use App\Http\Controllers\RotiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\LaporanController;

// Default
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', [UserController::class, 'profile'])->name('profile')->middleware('auth');
Route::get('/home/cetak-pdf', [App\Http\Controllers\HomeController::class, 'cetakPdf'])->name('home.cetakPdf');


// Untuk manager (akses semua)
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/filter', [LaporanController::class, 'filter'])->name('laporan.filter');
    Route::get('/laporan/cetak-pdf', [LaporanController::class, 'cetakPdf'])->name('laporan.cetakPdf');
});

// Untuk roti
Route::prefix('roti')->middleware(['auth', 'role:roti,manager'])->group(function () {
    Route::get('/', [RotiController::class, 'index'])->name('roti.index');
    Route::post('/', [RotiController::class, 'storeRoti'])->name('roti.store');
    Route::put('/{id}', [RotiController::class, 'update'])->name('roti.update');
    Route::delete('/{id}', [RotiController::class, 'destroy'])->name('roti.destroy');
    Route::get('/detail', [RotiController::class, 'detail'])->name('roti.detail');
    Route::post('/masuk', [RotiController::class, 'masuk'])->name('roti.masuk');
    Route::post('/keluar', [RotiController::class, 'keluar'])->name('roti.keluar');
    Route::get('/stok/{id}', [RotiController::class, 'stok'])->name('roti.stok');
});

// Untuk bar
Route::prefix('bar')->middleware(['auth', 'role:bar,manager'])->group(function () {
    Route::get('/', [BarController::class, 'index'])->name('bar.index');
    Route::post('/', [BarController::class, 'storebar'])->name('bar.store');
    Route::put('/{id}', [BarController::class, 'update'])->name('bar.update');
    Route::delete('/{id}', [BarController::class, 'destroy'])->name('bar.destroy');
    Route::get('/detail', [BarController::class, 'detail'])->name('bar.detail');
    Route::post('/masuk', [BarController::class, 'masuk'])->name('bar.masuk');
    Route::post('/keluar', [BarController::class, 'keluar'])->name('bar.keluar');
    Route::get('/stok/{id}', [BarController::class, 'stok'])->name('bar.stok');
});

// Untuk kitchen
Route::prefix('kitchen')->middleware(['auth', 'role:kitchen,manager'])->group(function () {
    Route::get('/', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/', [KitchenController::class, 'storekitchen'])->name('kitchen.store');
    Route::put('/{id}', [KitchenController::class, 'update'])->name('kitchen.update');
    Route::delete('/{id}', [KitchenController::class, 'destroy'])->name('kitchen.destroy');
    Route::get('/detail', [KitchenController::class, 'detail'])->name('kitchen.detail');
    Route::post('/masuk', [KitchenController::class, 'masuk'])->name('kitchen.masuk');
    Route::post('/keluar', [KitchenController::class, 'keluar'])->name('kitchen.keluar');
    Route::get('/stok/{id}', [KitchenController::class, 'stok'])->name('kitchen.stok');
});
