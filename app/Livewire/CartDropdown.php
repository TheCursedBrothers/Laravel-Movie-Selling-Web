<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;

class CartDropdown extends Component
{
    public $cartCount = 0;
    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount(CartService $cartService)
    {
        $this->cartCount = $cartService->count();
    }

    public function updateCartCount(CartService $cartService)
    {
        $this->cartCount = $cartService->count();
    }

    public function render()
    {
        return view('livewire.cart-dropdown');
    }
}
