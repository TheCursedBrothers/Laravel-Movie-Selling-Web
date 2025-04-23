<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Đảm bảo các lớp tồn tại trước khi đăng ký
        if (class_exists(\App\Livewire\AddToCartButton::class)) {
            Livewire::component('add-to-cart-button', \App\Livewire\AddToCartButton::class);
        }
        
        if (class_exists(\App\Livewire\CartDropdown::class)) {
            Livewire::component('cart-dropdown', \App\Livewire\CartDropdown::class);
        }
        
        if (class_exists(\App\Livewire\Notification::class)) {
            Livewire::component('notification', \App\Livewire\Notification::class);
        }
        
        if (class_exists(\App\Livewire\SearchDropdown::class)) {
            Livewire::component('search-dropdown', \App\Livewire\SearchDropdown::class);
        }
    }
}
