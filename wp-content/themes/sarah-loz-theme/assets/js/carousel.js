document.addEventListener('DOMContentLoaded', function() {
    initializeCarousel();
});

function initializeCarousel() {
    const carousel = document.querySelector('.games-carousel');
    if (!carousel) return;

    const slider = carousel.querySelector('.games-slider');
    const slides = carousel.querySelectorAll('.game-card');
    const prevBtn = carousel.querySelector('.carousel-prev');
    const nextBtn = carousel.querySelector('.carousel-next');

    if (!slider || !slides.length || !prevBtn || !nextBtn) return;

    let currentIndex = 0;
    const slidesToShow = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 640 ? 2 : 1;
    const totalSlides = slides.length;

    // Initialize slide positions
    updateSlidePositions();

    // Add event listeners for navigation
    prevBtn.addEventListener('click', showPreviousSlide);
    nextBtn.addEventListener('click', showNextSlide);

    // Add touch support
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateSlidePositions();
        }, 250);
    });

    function showNextSlide() {
        if (currentIndex < totalSlides - slidesToShow) {
            currentIndex++;
            updateSlidePositions();
        } else {
            // Animate bounce effect when reaching the end
            slider.style.transform = `translateX(${getTranslateX() - 20}px)`;
            setTimeout(() => {
                slider.style.transform = `translateX(${getTranslateX()}px)`;
            }, 200);
        }
    }

    function showPreviousSlide() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlidePositions();
        } else {
            // Animate bounce effect when reaching the start
            slider.style.transform = `translateX(${getTranslateX() + 20}px)`;
            setTimeout(() => {
                slider.style.transform = `translateX(${getTranslateX()}px)`;
            }, 200);
        }
    }

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchEndX - touchStartX;

        if (Math.abs(diff) >= swipeThreshold) {
            if (diff > 0) {
                showPreviousSlide();
            } else {
                showNextSlide();
            }
        }
    }

    function getTranslateX() {
        const slideWidth = slides[0].offsetWidth;
        const gap = 32; // This should match the gap in your CSS
        return -(currentIndex * (slideWidth + gap));
    }

    function updateSlidePositions() {
        const translateX = getTranslateX();
        slider.style.transform = `translateX(${translateX}px)`;
        
        // Update button states
        prevBtn.classList.toggle('opacity-50', currentIndex === 0);
        nextBtn.classList.toggle('opacity-50', currentIndex >= totalSlides - slidesToShow);
    }
}
