<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RajaOngkirController;

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

//Raja Ongkir
// Route::get('/list-ongkir', function () {
//     $response = Http::withHeaders([
//         'key' => env('RAJAONGKIR_API_KEY')
//     ])->get(env('RAJAONGKIR_BASE_URL').'/cost');

//     dd($response->json());
// });

// Route::get('/list-ongkir', function () {
//     $response = Http::withHeaders([
//         'key' => env('RAJAONGKIR_API_KEY'),
//     ])->get(env('RAJAONGKIR_BASE_URL') . '/cost/domestic-cost', [
//         'search' => 'sinduharjo',
//         'limit' => 5,
//         'offset' => 0,
//     ]);

//     dd([
//         'status' => $response->status(),
//         'body' => $response->json(),
//     ]);
// });

// Route::prefix('list-ongkir')->group(function () {
    
//     // GET /list-ongkir - Pencarian destination (endpoint yang terbukti bekerja)
//     Route::get('/', function () {
//         try {
//             $response = Http::withHeaders([
//                 'key' => env('RAJAONGKIR_API_KEY'),
//             ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/domestic-destination', [
//                 'search' => request('search', 'sinduharjo'),
//                 'limit' => request('limit', 5),
//                 'offset' => request('offset', 0),
//             ]);

//             $data = $response->json();
            
//             if ($response->successful() && $data['meta']['status'] === 'success') {
//                 return response()->json([
//                     'status' => 200,
//                     'message' => $data['meta']['message'],
//                     'results' => $data['data']
//                 ]);
//             }

//             return response()->json([
//                 'status' => $data['meta']['code'] ?? 500,
//                 'error' => $data['meta']['message'] ?? 'Unknown error'
//             ], $data['meta']['code'] ?? 500);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'status' => 500,
//                 'error' => 'Internal server error',
//                 'message' => $e->getMessage()
//             ], 500);
//         }
//     });

//     // // GET /list-ongkir/provinces
//     // Route::get('/provinces', function () {
//     //     $response = Http::withHeaders([
//     //         'key' => env('RAJAONGKIR_API_KEY')
//     //     ])->get(env('RAJAONGKIR_BASE_URL').'/province');

//     //     return response()->json([
//     //         'status' => $response->status(),
//     //         'data' => $response->json()
//     //     ]);
//     // });

//     // // GET /list-ongkir/cities
//     // Route::get('/cities', function () {
//     //     $response = Http::withHeaders([
//     //         'key' => env('RAJAONGKIR_API_KEY')
//     //     ])->get(env('RAJAONGKIR_BASE_URL').'/city');

//     //     return response()->json([
//     //         'status' => $response->status(),
//     //         'data' => $response->json()
//     //     ]);
//     // });

//     // // GET /list-ongkir/calculate
//     // Route::get('/calculate', function () {
//     //     $response = Http::withHeaders([
//     //         'key' => env('RAJAONGKIR_API_KEY'),
//     //     ])->get(env('RAJAONGKIR_BASE_URL').'/cost/domestic-cost', [
//     //         'origin' => request('origin'),
//     //         'destination' => request('destination'),
//     //         'weight' => request('weight'),
//     //         'courier' => request('courier'),
//     //     ]);

//     //     return response()->json([
//     //         'status' => $response->status(),
//     //         'data' => $response->json()
//     //     ]);
//     // });

//     // // GET /list-ongkir/search (alternatif pencarian)
//     // Route::get('/search', function () {
//     //     $response = Http::withHeaders([
//     //         'key' => env('RAJAONGKIR_API_KEY'),
//     //     ])->get(env('RAJAONGKIR_BASE_URL').'/cost/domestic-cost', [
//     //         'search' => request('search', 'sinduharjo'),
//     //         'limit' => request('limit', 5),
//     //         'offset' => request('offset', 0),
//     //     ]);

//     //     return response()->json([
//     //         'status' => $response->status(),
//     //         'data' => $response->json()
//     //     ]);
//     // });
// });

// // Halaman utama cek ongkir
// Route::get('/cek-ongkir', [RajaOngkirController::class, 'showForm']);
// Route::get('/destinations', [RajaOngkirController::class, 'searchDestinations']);
// Route::post('/calculate-cost', [RajaOngkirController::class, 'calculateCost']);

Route::get('/cek-ongkir', function () { 
    return view('ongkir'); 
}); 
 
Route::get('/location', [RajaOngkirController::class, 'getLocation']); //delivery
Route::post('/cost', [RajaOngkirController::class, 'getCost']); //cost

// Untuk debug atau testing list destination dengan search
Route::get('/list-ongkir', function () {
    $response = Http::withHeaders([
        'key' => env('RAJAONGKIR_API_KEY_COST'),
    ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/domestic-destination', [
        'search' => 'bekasi',
        'limit' => 10,
        'offset' => 0,
    ]);

    dd([
        'status' => $response->status(),
        'body' => $response->json(),
    ]);
});