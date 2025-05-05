<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('beranda');
});

// Frontend Routes
Route::middleware(['web'])->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda'); 
    Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
    Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])->name('produk.kategori');
    Route::get('/produk/all', [ProdukController::class, 'produkAll'])->name('produk.all');
});

// Backend Routes - Admin
Route::middleware(['auth'])->prefix('backend')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'berandaBackend'])->name('backend.beranda');
    
    // Authentication
    Route::get('/login', [LoginController::class, 'loginBackend'])->name('backend.login');
    Route::post('/login', [LoginController::class, 'authenticateBackend'])->name('backend.login.authenticate');
    Route::post('/logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');
    
    // Resources
    Route::resource('/user', UserController::class)->names('backend.user');
    Route::resource('/kategori', KategoriController::class)->names('backend.kategori');
    Route::resource('/produk', ProdukController::class)->names('backend.produk');
    
    // Produk Foto
    Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])->name('backend.foto_produk.store');
    Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])->name('backend.foto_produk.destroy');
    
    // Laporan
    Route::get('laporan/formuser', [UserController::class, 'formUser'])->name('backend.laporan.formuser');
    Route::post('laporan/cetakuser', [UserController::class, 'cetakUser'])->name('backend.laporan.cetakuser');
    Route::get('laporan/formproduk', [ProdukController::class, 'formProduk'])->name('backend.laporan.formproduk');
    Route::post('laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])->name('backend.laporan.cetakproduk');
    
    // Customer Management (for admin)
    Route::resource('/customer', CustomerController::class)->names('backend.customer');
});

// Google OAuth Routes - Updated method names
Route::get('/auth/redirect', [CustomerController::class, 'redirectToGoogle'])->name('auth.redirect');
Route::get('/auth/google/callback', [CustomerController::class, 'handleGoogleCallback'])->name('auth.callback');

// Customer Account Routes (for customers)
Route::middleware(['auth', 'is.customer'])->prefix('customer')->group(function () {
    Route::get('/akun/{id}', [CustomerController::class, 'akun'])->name('customer.akun');
    Route::put('/akun/{id}', [CustomerController::class, 'updateAkun'])->name('customer.akun.update');
        // Route untuk menambahkan produk ke keranjang 
        Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])->name('order.addToCart'); 
    
        // Route untuk melihat isi keranjang
        Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart'); 
    
        // Route untuk menghapus item dari keranjang
        Route::delete('remove-from-cart/{id}', [OrderController::class, 'removeCartItem'])->name('order.removeCartItem');
        // Route untuk mengupdate quantity item dalam keranjang
        Route::put('update-cart-item/{id}', [OrderController::class, 'updateCartItem'])->name('order.updateCartItem');
});

// Logout Route
Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');