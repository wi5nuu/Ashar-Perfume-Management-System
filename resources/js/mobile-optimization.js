// Mobile Optimization Script for APMS

(function() {
    'use strict';

    // 1. Viewport height fix for mobile browsers
    function setVhProperty() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setVhProperty();
    window.addEventListener('resize', setVhProperty);
    window.addEventListener('orientationchange', setVhProperty);

    // 2. Prevent zoom on input focus (iOS Safari)
    const metaViewport = document.querySelector('meta[name="viewport"]');
    if (metaViewport && /iPhone|iPad|iPod/.test(navigator.userAgent)) {
        metaViewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
    }

    // 3. Back to top button
    const backToTop = document.createElement('button');
    backToTop.className = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTop.setAttribute('aria-label', 'Kembali ke atas');
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    });

    backToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // 4. Touch-friendly table scrolling indicator
    function addTableScrollIndicators() {
        document.querySelectorAll('.table-responsive').forEach(table => {
            if (table.scrollWidth > table.clientWidth) {
                table.style.position = 'relative';
                
                const indicator = document.createElement('div');
                indicator.className = 'scroll-indicator';
                indicator.innerHTML = '<i class="fas fa-chevron-right"></i>';
                indicator.style.cssText = `
                    position: absolute;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(255,107,53,0.9);
                    color: white;
                    padding: 5px 8px;
                    border-radius: 4px 0 0 4px;
                    font-size: 0.7rem;
                    pointer-events: none;
                    z-index: 10;
                    animation: pulse 1.5s infinite;
                `;
                
                table.appendChild(indicator);
                
                table.addEventListener('scroll', function() {
                    if (this.scrollLeft > 20) {
                        indicator.style.display = 'none';
                    }
                });
            }
        });
    }

    // 5. Lazy load images
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    // 6. Optimize Select2 for mobile
    function optimizeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true,
                minimumResultsForSearch: 5,
                theme: 'bootstrap4'
            });
        }
    }

    // 7. Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // 8. Optimize DataTables for mobile
    function optimizeDataTables() {
        if (typeof $.fn.DataTable !== 'undefined') {
            const isMobile = window.innerWidth < 768;
            
            $.extend(true, $.fn.dataTable.defaults, {
                responsive: true,
                pageLength: isMobile ? 10 : 25,
                lengthMenu: isMobile ? [[10, 25], [10, 25]] : [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: isMobile ? "<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-6'i><'col-6'p>>" : "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>rt<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    search: isMobile ? '' : 'Cari:',
                    searchPlaceholder: 'Cari...',
                    lengthMenu: isMobile ? '_MENU_' : 'Tampilkan _MENU_ data',
                    info: isMobile ? '_START_-_END_ / _TOTAL_' : 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '',
                    paginate: {
                        first: isMobile ? '«' : 'Pertama',
                        last: isMobile ? '»' : 'Terakhir',
                        next: isMobile ? '›' : 'Selanjutnya',
                        previous: isMobile ? '‹' : 'Sebelumnya'
                    }
                }
            });
        }
    }

    // 9. Touch event optimization
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    // 10. Performance monitoring (optional)
    function logPerformance() {
        if (window.performance && window.performance.timing) {
            const timing = window.performance.timing;
            const loadTime = timing.loadEventEnd - timing.navigationStart;
            console.log(`Page load time: ${loadTime}ms`);
        }
    }

    // 11. Initialize everything when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        addTableScrollIndicators();
        lazyLoadImages();
        optimizeSelect2();
        optimizeDataTables();
        
        // Re-run table indicators on AJAX load
        $(document).ajaxComplete(debounce(addTableScrollIndicators, 300));
        
        // Performance log
        setTimeout(logPerformance, 0);
    }

    // 12. Register Service Worker for PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(
                registration => console.log('ServiceWorker registered'),
                err => console.log('ServiceWorker registration failed:', err)
            );
        });
    }

    // 13. Add install prompt for PWA
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Show install button (if you want to add one)
        const installBtn = document.getElementById('installPWA');
        if (installBtn) {
            installBtn.style.display = 'block';
            installBtn.addEventListener('click', () => {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                });
            });
        }
    });

})();
