<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminMoviesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API cart routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('api.cart.add');
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::delete('/cart/{movie}', [CartController::class, 'removeItem']);
    Route::patch('/cart/{item}', [CartController::class, 'updateQuantity']);
    Route::post('/cart/clear', [CartController::class, 'clear']);
});

// Payment IPN routes - make sure both GET and POST are supported for testing
Route::match(['get', 'post'], '/payments/momo/ipn', [PaymentController::class, 'momoIpn'])->name('payments.momo.ipn');

// API để thêm phim từ TMDB
Route::middleware('auth:sanctum')->post('/admin/movies', [AdminMoviesController::class, 'apiStore']);
