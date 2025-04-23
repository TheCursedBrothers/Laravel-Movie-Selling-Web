<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMoviesController;
use App\Http\Controllers\Admin\AdminOrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// Route hiển thị trang chủ với danh sách phim phổ biến
Route::get('/', [MoviesController::class, 'index'])->name('movie.index');

// Thêm route dashboard để redirect sau khi đăng nhập
Route::get('/dashboard', function () {
    return redirect()->route('movie.index');
})->middleware(['auth'])->name('dashboard');

// Route tìm kiếm phim - make sure this is properly configured
Route::get('/search', [MoviesController::class, 'search'])->name('movies.search');

// Route lọc phim theo năm, thể loại và quốc gia
Route::get('/movies/filter', [MoviesController::class, 'filter'])->name('movies.filter');

// Route hiển thị chi tiết của một phim cụ thể
Route::get('/movies/{movie}', [MoviesController::class, 'show'])->name('movies.show');

// Cart routes - thêm vào giỏ hàng từ AJAX request
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');

// Cart routes - Yêu cầu đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/{movie}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::patch('/cart/{item}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout routes
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Payment callback routes - Bỏ yêu cầu đăng nhập vì có thể đã hết session
Route::get('/payments/momo/callback', [PaymentController::class, 'momoCallback'])->name('payments.momo.callback');
Route::post('/payments/momo/ipn', [PaymentController::class, 'momoIpn'])->name('payments.momo.ipn');
Route::get('/payments/momo/cancel', [PaymentController::class, 'momoCancel'])->name('payments.momo.cancel');

// Cho phép xem trang success mà không cần đăng nhập (sẽ tự đăng nhập lại từ order_id)
Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');

// Payment test route
Route::get('/payments/momo/test', function () {
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

    echo "<h1>MoMo API Test</h1>";

    // Test basic connectivity
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_exec($ch);

    echo "<h2>Basic Connection Test</h2>";
    if (curl_errno($ch)) {
        echo "<p style='color: red'>Connection Error: " . curl_error($ch) . "</p>";
        echo "<p>Error Code: " . curl_errno($ch) . "</p>";
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "<p style='color: green'>Connection Successful! HTTP Code: " . $httpCode . "</p>";
    }

    $info = curl_getinfo($ch);
    curl_close($ch);

    echo "<h3>Request Details:</h3>";
    echo "<pre>" . print_r($info, true) . "</pre>";

    echo "<p><a href='/checkout' style='padding: 10px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Return to Checkout</a></p>";
});

// Profile routes with enhanced navigation
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Thêm các route riêng biệt cho các tính năng quản lý tài khoản
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/movies/favorites', [MoviesController::class, 'favorites'])->name('movies.favorites');
    Route::delete('/movies/favorites/{movie}', [MoviesController::class, 'removeFavorite'])->name('movies.favorites.remove');
    Route::post('/movies/favorites', [MoviesController::class, 'addToFavorites'])->name('movies.favorites.add');
});

// Admin routes - Không sử dụng middleware admin và prefix admin
Route::middleware(['auth'])->group(function () {
    // Add dashboard route
    Route::get('/admin', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Admin dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Quản lý phim
    Route::get('/admin/movies/search', [AdminMoviesController::class, 'search'])->name('admin.movies.search');
    Route::resource('/admin/movies', AdminMoviesController::class)->names('admin.movies');

    // Thêm route cập nhật phim một cách rõ ràng
    Route::put('/admin/movies/{movie}', [AdminMoviesController::class, 'update'])->name('admin.movies.update');
    Route::patch('/admin/movies/{movie}', [AdminMoviesController::class, 'update']);

    // Quản lý người dùng - Sửa đường dẫn để khớp với phương thức trong AdminController
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');

    // Quản lý đơn hàng
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::put('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
    Route::delete('/admin/orders/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

    // Make sure this route exists and is linked to the AdminOrdersController
    Route::get('/admin/orders/{order}/edit', [AdminOrdersController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/admin/orders/{order}', [AdminOrdersController::class, 'update'])->name('admin.orders.update');
});

// Admin Order routes - Chỉ sử dụng middleware auth, không dùng middleware admin
Route::prefix('admin/orders')->name('admin.orders.')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminOrdersController::class, 'index'])->name('index');
    Route::get('/export', [AdminOrdersController::class, 'export'])->name('export');
    Route::get('/{order}', [AdminOrdersController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [AdminOrdersController::class, 'edit'])->name('edit');
    Route::put('/{order}', [AdminOrdersController::class, 'update'])->name('update');
    Route::put('/{order}/status', [AdminOrdersController::class, 'updateOrderStatus'])->name('update-status');
    Route::post('/{order}/note', [AdminOrdersController::class, 'addNote'])->name('add-note');
    Route::delete('/{order}', [AdminOrdersController::class, 'destroy'])->name('destroy');
});

// Authentication routes
require __DIR__ . '/auth.php';
