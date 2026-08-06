// Animation Enhancements for APMS

(function() {
    'use strict';

    // 1. Fade-in animation on scroll (Intersection Observer)
    function fadeInOnScroll() {
        const elements = document.querySelectorAll('.fade-in-scroll');
        
        if (!elements.length) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-active');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(el => observer.observe(el));
    }

    // 2. Count-up animation for numbers
    function countUpAnimation() {
        const counters = document.querySelectorAll('.count-up');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target') || counter.textContent.replace(/[^0-9]/g, ''));
            const duration = parseInt(counter.getAttribute('data-duration') || 2000);
            const start = 0;
            const increment = target / (duration / 16);
            
            let current = start;
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('id-ID');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('id-ID');
                }
            };
            
            // Trigger when visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        updateCounter();
                        observer.unobserve(counter);
                    }
                });
            });
            
            observer.observe(counter);
        });
    }

    // 3. Ripple effect on buttons
    function addRippleEffect() {
        document.addEventListener('click', function(e) {
            const target = e.target.closest('.btn, .ripple');
            if (!target) return;
            
            const ripple = document.createElement('span');
            const rect = target.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');
            
            target.style.position = 'relative';
            target.style.overflow = 'hidden';
            target.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    }

    // 4. Skeleton loader animation
    function createSkeletonLoader(element) {
        element.classList.add('skeleton-loading');
        element.innerHTML = '<div class="skeleton-line"></div>'.repeat(3);
    }

    function removeSkeletonLoader(element, content) {
        element.classList.remove('skeleton-loading');
        element.innerHTML = content;
    }

    // Make globally available
    window.createSkeletonLoader = createSkeletonLoader;
    window.removeSkeletonLoader = removeSkeletonLoader;

    // 5. Stagger animation for lists
    function staggerAnimation() {
        const lists = document.querySelectorAll('.stagger-list');
        
        lists.forEach(list => {
            const items = list.querySelectorAll('.stagger-item');
            items.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.classList.add('stagger-animate');
            });
        });
    }

    // 6. Parallax effect (subtle)
    function parallaxEffect() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(el => {
                const speed = el.getAttribute('data-speed') || 0.5;
                const yPos = -(scrolled * speed);
                el.style.transform = `translateY(${yPos}px)`;
            });
        }, { passive: true });
    }

    // 7. Shake animation on error
    function shakeElement(element) {
        element.classList.add('shake-animation');
        setTimeout(() => element.classList.remove('shake-animation'), 500);
    }

    window.shakeElement = shakeElement;

    // 8. Pulse animation
    function pulseElement(element, duration = 1000) {
        element.classList.add('pulse-animation');
        setTimeout(() => element.classList.remove('pulse-animation'), duration);
    }

    window.pulseElement = pulseElement;

    // 9. Slide in from side
    function slideInElements() {
        const leftElements = document.querySelectorAll('.slide-in-left');
        const rightElements = document.querySelectorAll('.slide-in-right');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-in-active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        [...leftElements, ...rightElements].forEach(el => observer.observe(el));
    }

    // 10. Initialize all animations
    function initAnimations() {
        fadeInOnScroll();
        countUpAnimation();
        addRippleEffect();
        staggerAnimation();
        parallaxEffect();
        slideInElements();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAnimations);
    } else {
        initAnimations();
    }

    // Reinitialize on AJAX complete
    if (typeof $ !== 'undefined') {
        $(document).ajaxComplete(initAnimations);
    }

})();

// Add animation CSS
const animationStyles = document.createElement('style');
animationStyles.textContent = `
    /* Fade in on scroll */
    .fade-in-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .fade-in-scroll.fade-in-active {
        opacity: 1;
        transform: translateY(0);
    }

    /* Ripple effect */
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Skeleton loading */
    .skeleton-loading {
        pointer-events: none;
    }
    
    .skeleton-line {
        height: 16px;
        margin: 8px 0;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-shimmer 1.5s infinite;
        border-radius: 4px;
    }
    
    @keyframes skeleton-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Stagger animation */
    .stagger-animate {
        opacity: 0;
        transform: translateY(20px);
        animation: stagger-in 0.5s ease forwards;
    }
    
    @keyframes stagger-in {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Shake animation */
    .shake-animation {
        animation: shake 0.5s ease;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }

    /* Pulse animation */
    .pulse-animation {
        animation: pulse 1s ease;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Slide in animations */
    .slide-in-left {
        opacity: 0;
        transform: translateX(-50px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .slide-in-right {
        opacity: 0;
        transform: translateX(50px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .slide-in-left.slide-in-active,
    .slide-in-right.slide-in-active {
        opacity: 1;
        transform: translateX(0);
    }

    /* Reduce motion for accessibility */
    @media (prefers-reduced-motion: reduce) {
        .fade-in-scroll,
        .stagger-animate,
        .slide-in-left,
        .slide-in-right {
            animation: none !important;
            transition: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
    }
`;
document.head.appendChild(animationStyles);
