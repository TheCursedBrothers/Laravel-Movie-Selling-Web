/**
 * File này chứa mã JavaScript để khởi tạo và điều khiển slider trong trang
 *
 * Mô tả chức năng:
 * - Khởi tạo slider cho phần cast, phim tương tự và phim đề xuất
 * - Xử lý các nút điều hướng trái phải để chuyển slide
 * - Tính toán và giới hạn số lượng slide di chuyển dựa trên kích thước màn hình
 */

/**
 * Giải thích cách xây dựng slider tái sử dụng:
 * 1. Tạo hàm khởi tạo initializeSlider() nhận tham số tên lớp slider
 * 2. Sử dụng kỹ thuật dynamic selector để có thể áp dụng cho nhiều slider khác nhau
 * 3. Responsive: Điều chỉnh số lượng slides hiển thị theo kích thước màn hình
 * 4. Xử lý resize: Cập nhật lại kích thước và vị trí slides khi thay đổi màn hình
 * 5. Tái sử dụng: Có thể áp dụng cho bất kỳ slider nào với cùng cấu trúc HTML
 */
document.addEventListener('DOMContentLoaded', function() {
    // Hàm khởi tạo slider
    function initializeSlider(sliderClass) {
        const sliderWrapper = document.querySelector(`.${sliderClass}-wrapper`);
        const prevButton = document.querySelector(`.${sliderClass}-prev`);
        const nextButton = document.querySelector(`.${sliderClass}-next`);
        const slides = document.querySelectorAll(`.${sliderClass}-slide`);

        if (!sliderWrapper || !prevButton || !nextButton || slides.length === 0) return;

        let currentPosition = 0;
        let slidesToShow = 5; // Mặc định hiển thị 5 slides trên desktop

        // Điều chỉnh số lượng slides hiển thị dựa trên kích thước màn hình
        function updateSlidesToShow() {
            if (window.innerWidth < 640) { // Mobile
                slidesToShow = 1;
            } else if (window.innerWidth < 768) { // Small tablets
                slidesToShow = 2;
            } else if (window.innerWidth < 1024) { // Large tablets / small laptops
                slidesToShow = 3;
            } else if (window.innerWidth < 1280) { // Laptops / desktops
                slidesToShow = 4;
            } else { // Large screens
                slidesToShow = 5;
            }
            return slidesToShow;
        }

        // Cập nhật số lượng slides hiển thị ban đầu
        updateSlidesToShow();

        // Tính toán slideWidth dựa trên số lượng slides hiển thị
        const slideWidth = 100 / slidesToShow;
        const totalSlides = slides.length;
        const maxPosition = Math.max(0, totalSlides - slidesToShow);

        // Cập nhật chiều rộng của mỗi slide
        slides.forEach(slide => {
            slide.style.width = `${slideWidth}%`;
        });

        // Cập nhật vị trí của slider
        function updateSliderPosition() {
            sliderWrapper.style.transform = `translateX(-${currentPosition * slideWidth}%)`;
        }

        // Sự kiện nút previous
        prevButton.addEventListener('click', function() {
            currentPosition = Math.max(0, currentPosition - 1);
            updateSliderPosition();
        });

        // Sự kiện nút next
        nextButton.addEventListener('click', function() {
            currentPosition = Math.min(maxPosition, currentPosition + 1);
            updateSliderPosition();
        });

        // Cập nhật khi thay đổi kích thước màn hình
        window.addEventListener('resize', function() {
            const oldSlidesToShow = slidesToShow;
            const newSlidesToShow = updateSlidesToShow();

            // Nếu số lượng slides hiển thị thay đổi, cập nhật lại slider
            if (oldSlidesToShow !== newSlidesToShow) {
                // Cập nhật chiều rộng của các slides
                const newSlideWidth = 100 / newSlidesToShow;
                slides.forEach(slide => {
                    slide.style.width = `${newSlideWidth}%`;
                });

                // Tính toán lại maxPosition và vị trí hiện tại
                const newMaxPosition = Math.max(0, totalSlides - newSlidesToShow);
                currentPosition = Math.min(currentPosition, newMaxPosition);

                // Cập nhật vị trí của slider
                updateSliderPosition();
            }
        });

        // Khởi tạo vị trí ban đầu
        updateSliderPosition();
    }

    // Khởi tạo slider cho phần cast
    initializeSlider('cast-slider');

    // Khởi tạo slider cho phần phim tương tự
    initializeSlider('similar-slider');

    // Khởi tạo slider cho phần phim đề xuất
    initializeSlider('recommended-slider');
});
