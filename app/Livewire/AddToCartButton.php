<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AddToCartButton extends Component
{
    public $movieId;
    public $loading = false;

    public function mount($movieId)
    {
        $this->movieId = $movieId;
    }

    public function addToCart()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            // Chuyển hướng đến trang đăng nhập
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thêm phim vào giỏ hàng.');
        }
        
        $this->loading = true;

        try {
            // Gửi request AJAX đến endpoint của Laravel
            $response = Http::post(route('cart.add'), [
                'tmdbId' => $this->movieId,
            ]);

            if ($response->successful()) {
                // Thông báo thành công
                $this->dispatch('cartUpdated');
                $this->dispatch('notify', [
                    'message' => 'Đã thêm phim vào giỏ hàng!',
                    'type' => 'success'
                ]);
            } else {
                // Thông báo lỗi
                $this->dispatch('notify', [
                    'message' => 'Không thể thêm phim vào giỏ hàng!',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Lỗi: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
        
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.add-to-cart-button', [
            'isLoggedIn' => Auth::check()
        ]);
    }
}
