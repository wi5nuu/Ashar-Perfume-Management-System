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

// Read user context from meta tags injected by the layout
const userRoleMeta   = document.querySelector('meta[name="user-role"]');
const userBranchMeta = document.querySelector('meta[name="user-branch-id"]');
const userRole       = userRoleMeta   ? userRoleMeta.getAttribute('content')   : '';
const userBranchId   = userBranchMeta ? userBranchMeta.getAttribute('content') : '';

// Scoped channel names — match server-side broadcast channel definitions
const inventoryChannel     = userBranchId ? `inventory.${userBranchId}`     : 'inventory';
const dashboardChannel     = userBranchId ? `dashboard.${userBranchId}`     : 'dashboard';
const notificationsChannel = userBranchId ? `notifications.${userBranchId}` : 'notifications';

// Listener untuk Stock Update
window.Echo.private(inventoryChannel)
    .listen('.StockUpdated', (e) => {
        if (typeof Toast !== 'undefined') {
            Toast.fire({ icon: 'info', title: '📦 Update Stok: ' + e.productName + ' (' + e.newStock + ')' });
        }
    });

// Listener untuk Live Dashboard Counters
window.Echo.private(dashboardChannel)
    .listen('.dashboard.updated', (e) => {
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
if (userRole === 'owner' || userRole === 'admin') {
    window.Echo.private('debt-approvals')
        .listen('.debt.submitted', (e) => {
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
window.Echo.private(notificationsChannel)
    .listen('.LowStockAlert', (e) => {
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
