import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Listener untuk Stock Update
window.Echo.channel('inventory')
    .listen('.StockUpdated', (e) => {
        console.log('[Realtime] Stok produk ' + e.productName + ' berubah menjadi: ' + e.newStock);
        if (typeof Toast !== 'undefined') {
            Toast.fire({ icon: 'info', title: '📦 Update Stok: ' + e.productName + ' (' + e.newStock + ')' });
        }
    });

// Listener untuk Live Dashboard Counters
window.Echo.channel('dashboard')
    .listen('.dashboard.updated', (e) => {
        console.log('[Realtime] Dashboard update', e);

        const txEl   = document.getElementById('dashboard-total-transactions');
        const revEl  = document.getElementById('dashboard-total-revenue');
        const stkEl  = document.getElementById('dashboard-low-stock');
        const debtEl = document.getElementById('dashboard-pending-debts');

        if (txEl)   txEl.textContent   = e.totalTransactionsToday;
        if (revEl)  revEl.textContent  = 'Rp ' + Number(e.totalRevenueToday).toLocaleString('id-ID');
        if (stkEl)  stkEl.textContent  = e.lowStockCount;
        if (debtEl) debtEl.textContent = e.pendingDebtsCount;
    });

// Listener untuk Notifikasi Hutang Baru (hanya owner/admin)
const debtChannel = document.querySelector('meta[name="user-role"]');
const userRole    = debtChannel ? debtChannel.getAttribute('content') : '';

if (userRole === 'owner' || userRole === 'admin') {
    window.Echo.private('debt-approvals')
        .listen('.debt.submitted', (e) => {
            console.log('[Realtime] Hutang baru masuk', e);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon:              'warning',
                    title:             '💳 Hutang Baru!',
                    html:              `<b>${e.cashierName}</b> mencatat hutang <b>Rp ${Number(e.debtAmount).toLocaleString('id-ID')}</b> untuk <b>${e.customerName}</b>.<br><small>Invoice: ${e.invoiceNumber}</small>`,
                    toast:             true,
                    position:          'top-end',
                    showConfirmButton: false,
                    timer:             8000,
                    timerProgressBar:  true,
                });
            }
        });
}

// Real-time notification channel — low stock, new wholesale orders, debt reminders
window.Echo.channel('notifications')
    .listen('.LowStockAlert', (e) => {
        console.log('[Realtime] Low stock alert', e);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: e.severity === 'critical' ? 'error' : 'warning',
                title: e.title,
                text: e.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 8000,
                timerProgressBar: true,
            });
        }
        updateNotifBadge(e.type);
    })
    .listen('.NewWholesaleOrder', (e) => {
        console.log('[Realtime] New wholesale order', e);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: e.title,
                text: e.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
            });
        }
        updateNotifBadge(e.type);
    })
    .listen('.DebtDueReminder', (e) => {
        console.log('[Realtime] Debt due reminder', e);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: e.title,
                text: e.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 8000,
                timerProgressBar: true,
            });
        }
        updateNotifBadge(e.type);
    });

function updateNotifBadge(type) {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        const count = parseInt(badge.textContent) || 0;
        badge.textContent = count + 1;
        badge.style.display = 'inline-block';
    }
}
