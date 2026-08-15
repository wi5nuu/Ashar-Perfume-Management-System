// Utility Functions for APMS

(function() {
    'use strict';

    // 1. Format currency (Indonesian Rupiah)
    window.formatCurrency = function(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    };

    // 2. Format number
    window.formatNumber = function(number, decimals = 0) {
        return parseFloat(number).toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    };

    // 3. Format date (Indonesian)
    window.formatDate = function(dateString, format = 'long') {
        const date = new Date(dateString);
        const options = format === 'long' 
            ? { year: 'numeric', month: 'long', day: 'numeric' }
            : { year: 'numeric', month: '2-digit', day: '2-digit' };
        
        return date.toLocaleDateString('id-ID', options);
    };

    // 4. Format time
    window.formatTime = function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    };

    // 5. Format datetime
    window.formatDateTime = function(dateString) {
        return formatDate(dateString) + ' ' + formatTime(dateString);
    };

    // 6. Debounce function
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // 7. Throttle function
    window.throttle = function(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    };

    // 8. Generate random ID
    window.generateId = function(prefix = 'id') {
        return prefix + '-' + Math.random().toString(36).substring(2, 11);
    };

    // 9. Parse query string
    window.parseQuery = function(queryString = window.location.search) {
        const params = new URLSearchParams(queryString);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    };

    // 10. Build query string
    window.buildQuery = function(params) {
        return Object.keys(params)
            .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key]))
            .join('&');
    };

    // 11. Deep clone object
    window.deepClone = function(obj) {
        return JSON.parse(JSON.stringify(obj));
    };

    // 12. Check if mobile
    window.isMobile = function() {
        return window.innerWidth < 768;
    };

    // 13. Check if tablet
    window.isTablet = function() {
        return window.innerWidth >= 768 && window.innerWidth < 992;
    };

    // 14. Check if desktop
    window.isDesktop = function() {
        return window.innerWidth >= 992;
    };

    // 15. Get device type
    window.getDeviceType = function() {
        if (isMobile()) return 'mobile';
        if (isTablet()) return 'tablet';
        return 'desktop';
    };

    // 16. Scroll to element
    window.scrollToElement = function(element, offset = 0) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (el) {
            const top = el.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    };

    // 17. Get element offset
    window.getOffset = function(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        const rect = el.getBoundingClientRect();
        return {
            top: rect.top + window.pageYOffset,
            left: rect.left + window.pageXOffset
        };
    };

    // 18. Check if in viewport
    window.isInViewport = function(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    };

    // 19. Local storage helpers
    window.storage = {
        set: function(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (e) {
                console.error('Storage error:', e);
                return false;
            }
        },
        get: function(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (e) {
                console.error('Storage error:', e);
                return defaultValue;
            }
        },
        remove: function(key) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (e) {
                console.error('Storage error:', e);
                return false;
            }
        },
        clear: function() {
            try {
                localStorage.clear();
                return true;
            } catch (e) {
                console.error('Storage error:', e);
                return false;
            }
        }
    };

    // 20. Wait function (async)
    window.wait = function(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    };

    // 21. Retry function with exponential backoff
    window.retry = async function(fn, maxAttempts = 3, delay = 1000) {
        for (let i = 0; i < maxAttempts; i++) {
            try {
                return await fn();
            } catch (error) {
                if (i === maxAttempts - 1) throw error;
                await wait(delay * Math.pow(2, i));
            }
        }
    };

    // 22. Validate email
    window.isValidEmail = function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };

    // 23. Validate phone (Indonesian)
    window.isValidPhone = function(phone) {
        const re = /^(\+62|62|0)[0-9]{9,12}$/;
        return re.test(phone.replace(/[\s-]/g, ''));
    };

    // 24. Sanitize HTML
    window.sanitizeHTML = function(str) {
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    };

    // 25. Truncate text
    window.truncate = function(str, length, suffix = '...') {
        if (str.length <= length) return str;
        return str.substring(0, length) + suffix;
    };

    // 26. Capitalize first letter
    window.capitalize = function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

    // 27. Title case
    window.titleCase = function(str) {
        return str.toLowerCase().split(' ').map(capitalize).join(' ');
    };

    // 28. Slugify
    window.slugify = function(str) {
        return str
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    };

    // 29. Random number
    window.random = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    };

    // 30. Array shuffle
    window.shuffle = function(array) {
        const arr = [...array];
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    };

    // 31. Array unique
    window.unique = function(array) {
        return [...new Set(array)];
    };

    // 32. Group by
    window.groupBy = function(array, key) {
        return array.reduce((result, item) => {
            (result[item[key]] = result[item[key]] || []).push(item);
            return result;
        }, {});
    };

    // 33. Sort by key
    window.sortBy = function(array, key, order = 'asc') {
        return [...array].sort((a, b) => {
            if (order === 'asc') {
                return a[key] > b[key] ? 1 : -1;
            } else {
                return a[key] < b[key] ? 1 : -1;
            }
        });
    };

    // 34. Download file
    window.downloadFile = function(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename || '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    // 35. Print element
    window.printElement = function(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<link rel="stylesheet" href="/build/assets/app.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(el.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    };

    // Log utilities loaded
    console.log('✅ APMS Utils loaded - 35 helper functions available');

})();

// jQuery utilities (if jQuery is loaded)
if (typeof $ !== 'undefined') {
    // Serialize form to object
    $.fn.serializeObject = function() {
        const obj = {};
        const arr = this.serializeArray();
        $.each(arr, function() {
            if (obj[this.name]) {
                if (!obj[this.name].push) {
                    obj[this.name] = [obj[this.name]];
                }
                obj[this.name].push(this.value || '');
            } else {
                obj[this.name] = this.value || '';
            }
        });
        return obj;
    };

    // Disable form
    $.fn.disableForm = function() {
        this.find('input, select, textarea, button').prop('disabled', true);
        return this;
    };

    // Enable form
    $.fn.enableForm = function() {
        this.find('input, select, textarea, button').prop('disabled', false);
        return this;
    };

    // Loading state
    $.fn.loading = function(state = true) {
        if (state) {
            this.prop('disabled', true);
            this.data('original-html', this.html());
            this.html('<i class="fas fa-spinner fa-spin mr-2"></i>Loading...');
        } else {
            this.prop('disabled', false);
            this.html(this.data('original-html'));
        }
        return this;
    };

    console.log('✅ jQuery utilities loaded');
}
