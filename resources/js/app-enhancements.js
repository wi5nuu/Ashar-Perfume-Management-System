// Application Enhancements & Bug Fixes

(function() {
    'use strict';

    // 1. Fix AdminLTE Sidebar on Mobile
    function fixMobileSidebar() {
        if (window.innerWidth < 768) {
            // Close sidebar by default on mobile
            $('body').removeClass('sidebar-open').addClass('sidebar-closed sidebar-collapse');
            
            // Add overlay when sidebar opens
            $(document).on('click', '[data-widget="pushmenu"]', function() {
                if ($('body').hasClass('sidebar-open')) {
                    // Sidebar is opening, add overlay
                    if (!$('.sidebar-overlay').length) {
                        $('<div class="sidebar-overlay"></div>')
                            .appendTo('body')
                            .on('click', function() {
                                $('[data-widget="pushmenu"]').trigger('click');
                                $(this).remove();
                            });
                    }
                } else {
                    // Sidebar is closing, remove overlay
                    $('.sidebar-overlay').remove();
                }
            });
        }
    }

    // 2. Fix DataTables Mobile Responsiveness
    function enhanceDataTables() {
        if (typeof $.fn.DataTable !== 'undefined') {
            // Set default options for all DataTables
            $.extend(true, $.fn.dataTable.defaults, {
                responsive: true,
                autoWidth: false,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin fa-2x"></i>',
                    emptyTable: 'Tidak ada data tersedia',
                    zeroRecords: 'Tidak ada data yang cocok',
                    loadingRecords: 'Memuat...',
                    paginate: {
                        first: '«',
                        last: '»',
                        next: '›',
                        previous: '‹'
                    }
                }
            });

            // Reinitialize existing tables
            $('.table').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().responsive.recalc();
                }
            });
        }
    }

    // 3. Fix Select2 Mobile
    function enhanceSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        dropdownAutoWidth: true,
                        minimumResultsForSearch: 5
                    });
                }
            });

            // Fix mobile touch
            if (window.innerWidth < 768) {
                $(document).on('select2:open', () => {
                    document.querySelector('.select2-search__field').focus();
                });
            }
        }
    }

    // 4. Auto-hide Alerts
    function autoHideAlerts() {
        $('.alert:not(.alert-permanent)').each(function() {
            const $alert = $(this);
            setTimeout(() => {
                $alert.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        });
    }

    // 5. Confirm Delete Actions
    function confirmDeleteActions() {
        $(document).on('submit', 'form[method="POST"]', function(e) {
            const form = $(this);
            const method = form.find('input[name="_method"]').val();
            
            if (method === 'DELETE') {
                e.preventDefault();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.off('submit').submit();
                        }
                    });
                } else {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        form.off('submit').submit();
                    }
                }
                
                return false;
            }
        });
    }

    // 6. Fix Number Input (prevent non-numeric)
    function fixNumberInputs() {
        $(document).on('input', 'input[type="number"]', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    }

    // 7. Auto-format Currency Inputs
    function formatCurrencyInputs() {
        $(document).on('blur', '.currency-input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value) {
                $(this).val(parseInt(value).toLocaleString('id-ID'));
            }
        });

        $(document).on('focus', '.currency-input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(value);
        });
    }

    // 8. Fix Image Loading Errors
    function fixImageErrors() {
        $(document).on('error', 'img', function() {
            if (!$(this).hasClass('error-handled')) {
                $(this).addClass('error-handled');
                $(this).attr('src', '/images/placeholder.png');
                // If placeholder also fails, use a data URI
                $(this).one('error', function() {
                    $(this).attr('src', 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect fill="%23ddd" width="100" height="100"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E');
                });
            }
        });
    }

    // 9. Prevent Double Form Submission
    function preventDoubleSubmit() {
        $(document).on('submit', 'form', function() {
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            
            if ($form.data('submitting') === true) {
                return false;
            }
            
            $form.data('submitting', true);
            $submitBtn.prop('disabled', true);
            
            // Re-enable after 3 seconds (safety)
            setTimeout(() => {
                $form.data('submitting', false);
                $submitBtn.prop('disabled', false);
            }, 3000);
        });
    }

    // 10. Fix Modal Scroll on Mobile
    function fixModalScroll() {
        $('.modal').on('shown.bs.modal', function() {
            $('body').addClass('modal-open-fixed');
        }).on('hidden.bs.modal', function() {
            $('body').removeClass('modal-open-fixed');
        });
    }

    // 11. Auto-focus First Input in Modals
    function autoFocusModals() {
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('input:not([type="hidden"]):first').focus();
        });
    }

    // 12. Loading State for AJAX
    function setupAjaxLoading() {
        let ajaxCount = 0;

        $(document).ajaxStart(function() {
            ajaxCount++;
            if (ajaxCount === 1) {
                $('body').append('<div class="ajax-loading"><i class="fas fa-spinner fa-spin fa-3x"></i></div>');
            }
        });

        $(document).ajaxStop(function() {
            ajaxCount--;
            if (ajaxCount === 0) {
                $('.ajax-loading').remove();
            }
        });

        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            console.error('AJAX Error:', thrownError);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat memuat data. Silakan coba lagi.'
                });
            }
        });
    }

    // 13. Fix Dropdown Position on Mobile
    function fixDropdownPosition() {
        if (window.innerWidth < 768) {
            $('.dropdown-menu').each(function() {
                const $menu = $(this);
                const $toggle = $menu.prev('.dropdown-toggle');
                
                $toggle.on('click', function() {
                    setTimeout(() => {
                        const rect = $menu[0].getBoundingClientRect();
                        if (rect.bottom > window.innerHeight) {
                            $menu.addClass('dropdown-menu-up');
                        }
                        if (rect.right > window.innerWidth) {
                            $menu.addClass('dropdown-menu-right');
                        }
                    }, 10);
                });
            });
        }
    }

    // 14. Smooth Scroll to Validation Errors
    function scrollToErrors() {
        if ($('.is-invalid').length) {
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
        }
    }

    // 15. Copy to Clipboard Helper
    function setupCopyButtons() {
        $(document).on('click', '[data-copy]', function(e) {
            e.preventDefault();
            const text = $(this).data('copy');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Copied to clipboard!', 'success');
                });
            } else {
                // Fallback for older browsers
                const $temp = $('<textarea>').val(text).appendTo('body').select();
                document.execCommand('copy');
                $temp.remove();
                showToast('Copied to clipboard!', 'success');
            }
        });
    }

    // 16. Toast Notification Helper
    function showToast(message, type = 'info') {
        if (typeof Swal !== 'undefined' && typeof Swal.mixin === 'function') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: type,
                title: message
            });
        } else {
            // Fallback to simple alert
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            }[type] || 'alert-info';

            const $toast = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `);

            $('body').append($toast);
            setTimeout(() => $toast.alert('close'), 3000);
        }
    }

    // Make showToast globally available
    window.showToast = showToast;

    // 17. Initialize Everything on Document Ready
    $(document).ready(function() {
        fixMobileSidebar();
        enhanceDataTables();
        enhanceSelect2();
        autoHideAlerts();
        confirmDeleteActions();
        fixNumberInputs();
        formatCurrencyInputs();
        fixImageErrors();
        preventDoubleSubmit();
        fixModalScroll();
        autoFocusModals();
        setupAjaxLoading();
        fixDropdownPosition();
        scrollToErrors();
        setupCopyButtons();

        // Reinitialize on AJAX complete
        $(document).ajaxComplete(function() {
            enhanceSelect2();
            enhanceDataTables();
        });
    });

    // 18. Reinitialize on Window Resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (typeof $.fn.DataTable !== 'undefined') {
                $('.table').each(function() {
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().responsive.recalc();
                    }
                });
            }
        }, 250);
    });

})();

// Add custom CSS for enhancements
const style = document.createElement('style');
style.textContent = `
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        display: none;
    }
    
    @media (max-width: 767.98px) {
        .sidebar-open .sidebar-overlay {
            display: block;
        }
    }
    
    .ajax-loading {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        color: #FF6B35;
        background: rgba(255, 255, 255, 0.95);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-open-fixed {
        overflow: hidden !important;
    }
    
    .dropdown-menu-up {
        bottom: 100% !important;
        top: auto !important;
    }
`;
document.head.appendChild(style);
