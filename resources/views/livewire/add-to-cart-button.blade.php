<div>
    @if($isLoggedIn)
        <button 
            wire:click="addToCart"
            wire:loading.attr="disabled"
            class="movie-btn relative"
            {{ $loading ? 'disabled' : '' }}
        >
            <span wire:loading.class="opacity-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Thêm vào giỏ hàng
            </span>
            <div wire:loading class="absolute inset-0 flex items-center justify-center">
                <div class="spinner"></div>
            </div>
        </button>
    @else
        <a href="{{ route('login') }}" class="movie-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Đăng nhập để mua hàng
        </a>
    @endif
</div>

@push('scripts')
<script>
    // Thêm sự kiện click cho tất cả các nút "Thêm vào giỏ hàng"
    document.addEventListener('DOMContentLoaded', function() {
        // Livewire sẽ xử lý việc thêm vào giỏ hàng, script này chỉ để minh họa cách dùng AJAX
        // Thực tế bạn sẽ dùng một trong hai cách: Livewire hoặc AJAX
        
        /* 
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const movieId = this.dataset.movieId;
                const button = this;
                
                // Hiện loading
                button.disabled = true;
                button.querySelector('span').innerText = 'Đang thêm...';
                
                // Gửi request AJAX
                fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        tmdbId: movieId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật giao diện
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement) {
                            cartCountElement.textContent = data.cart_count;
                        }
                        
                        // Hiển thị thông báo
                        showNotification('Đã thêm phim vào giỏ hàng', 'success');
                    } else {
                        showNotification(data.message || 'Có lỗi xảy ra', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
                })
                .finally(() => {
                    // Khôi phục nút
                    button.disabled = false;
                    button.querySelector('span').innerText = 'Thêm vào giỏ';
                });
            });
        });
        
        // Hàm hiển thị thông báo
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-5 right-5 z-50 p-4 rounded-lg ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white flex items-center shadow-lg`;
            
            notification.innerHTML = `
                <div class="mr-3">
                    ${type === 'success' 
                        ? '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                        : '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                    }
                </div>
                <div>${message}</div>
                <button type="button" class="ml-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            document.body.appendChild(notification);
            
            // Xóa thông báo sau 3 giây
            setTimeout(() => {
                notification.remove();
            }, 3000);
            
            // Xử lý nút đóng
            notification.querySelector('button').addEventListener('click', function() {
                notification.remove();
            });
        }
        */
    });
</script>
@endpush
