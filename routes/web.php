<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierPriceController;
use App\Http\Controllers\CashReconciliationController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\BulkPriceController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\OwnerController;

Route::middleware(['auth', 'throttle:100,1'])->group(function () {
    // 👑 COMMON: Staff-only routes (excludes wholesale_customer)
    Route::middleware(['verified', 'role:owner,admin,admin_pusat,manager,cashier,supervisor,warehouse'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
        Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->middleware('throttle:10,1')->name('settings.profile.update');
        Route::post('/settings/password', [SettingController::class, 'updatePassword'])->middleware('throttle:5,1')->name('settings.password.update');
        Route::delete('/settings/profile', [SettingController::class, 'destroyProfile'])->middleware('throttle:3,1')->name('settings.profile.destroy');

        Route::post('/settings/password/reset-request', [SettingController::class, 'requestPasswordReset'])->middleware('throttle:3,10')->name('settings.password.reset-request');
        Route::get('/settings/password/reset-requests', [SettingController::class, 'resetRequests'])->name('settings.password.reset-requests');
        Route::post('/settings/password/reset-requests/{resetRequest}/approve', [SettingController::class, 'approveReset'])->middleware('throttle:10,1')->name('settings.password.reset-approve');
        Route::post('/settings/password/reset-requests/{resetRequest}/reject', [SettingController::class, 'rejectReset'])->middleware('throttle:10,1')->name('settings.password.reset-reject');
    });

    // 💰 POS & SHIFT: Cashier, Admin, Manager, Owner, Supervisor (attendance)
    Route::middleware(['verified', 'role:owner,admin,admin_pusat,manager,cashier,supervisor'])->group(function () {
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->middleware('throttle:20,1')->name('transactions.store');
        Route::get('/api/products/{id}', [TransactionController::class, 'getProductInfo']);
        Route::get('transactions/customer-info/{id}', [TransactionController::class, 'getCustomerInfo'])->name('transactions.customer-info');
        
        // Attendances
        Route::post('attendances', [\App\Http\Controllers\AttendanceController::class, 'store'])->middleware('throttle:10,1')->name('attendances.store');
        Route::post('attendances/{attendance}/checkout', [\App\Http\Controllers\AttendanceController::class, 'checkout'])->middleware('throttle:10,1')->name('attendances.checkout');
        Route::get('attendances', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendances.index');

        // Shift Management
        Route::resource('shifts', \App\Http\Controllers\ShiftController::class)->parameters(['shifts' => 'shift']);

        // Cash Reconciliation
        Route::get('/shifts/{shift}/reconciliation', [CashReconciliationController::class, 'show'])->name('shifts.reconciliation');
        Route::post('/shifts/{shift}/reconciliation', [CashReconciliationController::class, 'store'])->name('shifts.reconciliation.store');
        Route::post('/shifts/{shift}/reconciliation/review', [CashReconciliationController::class, 'review'])->name('shifts.reconciliation.review');
    });

    // 📦 CATALOG & OPERATIONS: Admin, Manager, Owner, Warehouse
    Route::middleware(['verified', 'role:owner,admin,admin_pusat,manager,warehouse'])->group(function () {
        // Products
        Route::get('/products/export/pdf', [ProductController::class, 'exportPDF'])->name('products.export.pdf');
        Route::get('/products/export/csv', [ProductController::class, 'exportCSV'])->name('products.export.csv');
        Route::resource('products', ProductController::class);
        Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::get('/products/{product}/barcode', [ProductController::class, 'printBarcode'])->name('products.barcode');
        Route::get('/products/{product}/barcode-image', [ProductController::class, 'renderBarcode'])->name('products.barcode-image');

        // Bulk Price Update
        Route::get('/bulk-price', [BulkPriceController::class, 'index'])->name('bulk-price.index');
        Route::post('/bulk-price', [BulkPriceController::class, 'update'])->middleware('throttle:5,1')->name('bulk-price.update');
        
        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->middleware('throttle:10,1')->name('inventory.adjust');
        Route::post('/inventory/audit', [InventoryController::class, 'audit'])->middleware('throttle:5,1')->name('inventory.audit');

        // Expiry Alerts
        Route::get('/inventory/expiry-alerts', [\App\Http\Controllers\ExpiryAlertController::class, 'index'])->name('inventory.expiry-alerts');
        Route::post('/inventory/{inventory}/dismiss-expiry', [\App\Http\Controllers\ExpiryAlertController::class, 'dismiss'])->name('inventory.dismiss-expiry');
        
                // Warehouses
                Route::resource('warehouses', WarehouseController::class);

                // Goods Receipts (Barang Masuk)
                Route::get('/goods-receipts', [\App\Http\Controllers\GoodsReceiptController::class, 'index'])->name('goods-receipts.index');
                Route::get('/goods-receipts/create', [\App\Http\Controllers\GoodsReceiptController::class, 'create'])->name('goods-receipts.create');
                Route::post('/goods-receipts', [\App\Http\Controllers\GoodsReceiptController::class, 'store'])->middleware('throttle:10,1')->name('goods-receipts.store');
                Route::get('/goods-receipts/{goodsReceipt}', [\App\Http\Controllers\GoodsReceiptController::class, 'show'])->name('goods-receipts.show');

        // Purchase Orders
        Route::get('/purchase-orders/supplier-prices/{supplier}', [PurchaseOrderController::class, 'getSupplierPrices'])->name('purchase-orders.supplier-prices');
        Route::post('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->middleware('throttle:10,1')->name('purchase-orders.send');
        Route::get('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'showReceive'])->name('purchase-orders.receive-form');
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->middleware('throttle:10,1')->name('purchase-orders.receive');
        Route::post('/purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->middleware('throttle:10,1')->name('purchase-orders.cancel');
        Route::resource('purchase-orders', PurchaseOrderController::class)->parameters(['purchase-orders' => 'purchaseOrder']);

        // Supplier Prices
        Route::resource('supplier-prices', SupplierPriceController::class)->only(['index', 'store', 'destroy'])->parameters(['supplier-prices' => 'supplierPrice']);
        Route::delete('/supplier-prices/{supplierPrice}', [SupplierPriceController::class, 'destroy'])->name('supplier-prices.destroy');
        
        // Customers & Coupons
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
        Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
        Route::post('/customers/{customer}/portal-token', [CustomerPortalController::class, 'generateToken'])->name('customers.portal-token');
        Route::resource('customers', CustomerController::class);
        Route::resource('coupons', CouponController::class);
        Route::post('/coupons/{coupon}/redeem', [CouponController::class, 'redeem'])->middleware('throttle:10,1')->name('coupons.redeem');

        // Wholesale Products (BEFORE wholesale resource to avoid {order} matching "products")
        Route::get('/wholesale/products', [\App\Http\Controllers\WholesaleProductController::class, 'index'])->name('wholesale.products.index');
        Route::get('/wholesale/products/create', [\App\Http\Controllers\WholesaleProductController::class, 'create'])->name('wholesale.products.create');
        Route::post('/wholesale/products', [\App\Http\Controllers\WholesaleProductController::class, 'store'])->middleware('throttle:10,1')->name('wholesale.products.store');
        Route::get('/wholesale/products/{wholesaleProduct}', [\App\Http\Controllers\WholesaleProductController::class, 'show'])->name('wholesale.products.show');
        Route::get('/wholesale/products/{wholesaleProduct}/edit', [\App\Http\Controllers\WholesaleProductController::class, 'edit'])->name('wholesale.products.edit');
        Route::put('/wholesale/products/{wholesaleProduct}', [\App\Http\Controllers\WholesaleProductController::class, 'update'])->middleware('throttle:10,1')->name('wholesale.products.update');
        Route::delete('/wholesale/products/{wholesaleProduct}', [\App\Http\Controllers\WholesaleProductController::class, 'destroy'])->name('wholesale.products.destroy');

        // Wholesale
        Route::get('/wholesale', [\App\Http\Controllers\WholesaleController::class, 'index'])->name('wholesale.index');
        Route::get('/wholesale/create', [\App\Http\Controllers\WholesaleController::class, 'create'])->name('wholesale.create');
        Route::post('/wholesale', [\App\Http\Controllers\WholesaleController::class, 'store'])->middleware('throttle:10,1')->name('wholesale.store');
        Route::get('/wholesale/{order}', [\App\Http\Controllers\WholesaleController::class, 'show'])->name('wholesale.show');
        Route::get('/wholesale/{order}/edit', [\App\Http\Controllers\WholesaleController::class, 'edit'])->name('wholesale.edit');
        Route::put('/wholesale/{order}', [\App\Http\Controllers\WholesaleController::class, 'update'])->middleware('throttle:10,1')->name('wholesale.update');
        Route::delete('/wholesale/{order}', [\App\Http\Controllers\WholesaleController::class, 'destroy'])->name('wholesale.destroy');
        Route::post('/wholesale/{order}/confirm', [\App\Http\Controllers\WholesaleController::class, 'confirm'])->middleware('throttle:10,1')->name('wholesale.confirm');
        Route::post('/wholesale/{order}/process', [\App\Http\Controllers\WholesaleController::class, 'process'])->middleware('throttle:10,1')->name('wholesale.process');
        Route::post('/wholesale/{order}/pack', [\App\Http\Controllers\WholesaleController::class, 'markPacked'])->middleware('throttle:10,1')->name('wholesale.pack');
        Route::post('/wholesale/{order}/ship', [\App\Http\Controllers\WholesaleController::class, 'markShipped'])->middleware('throttle:10,1')->name('wholesale.ship');
        Route::post('/wholesale/{order}/deliver', [\App\Http\Controllers\WholesaleController::class, 'markDelivered'])->middleware('throttle:10,1')->name('wholesale.deliver');
        Route::post('/wholesale/{order}/complete', [\App\Http\Controllers\WholesaleController::class, 'complete'])->middleware('throttle:10,1')->name('wholesale.complete');
        Route::post('/wholesale/{order}/cancel', [\App\Http\Controllers\WholesaleController::class, 'cancel'])->middleware('throttle:10,1')->name('wholesale.cancel');
        Route::get('/wholesale/{order}/print', [\App\Http\Controllers\WholesaleController::class, 'print'])->name('wholesale.print');

        // Transactions viewing
        Route::resource('transactions', TransactionController::class)->except(['create', 'store']);
        Route::get('/transactions/{transaction}/print', [TransactionController::class, 'printInvoice'])->name('transactions.print');
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'printInvoice'])->name('transactions.receipt');

        // Extra Ops
        Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->middleware('throttle:10,1')->name('expenses.store');
        Route::get('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
        Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'update'])->middleware('throttle:10,1')->name('expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::resource('stock_audits', \App\Http\Controllers\StockAuditController::class);
        Route::post('stock_audits/{stock_audit}/update-items', [\App\Http\Controllers\StockAuditController::class, 'updateItems'])->middleware('throttle:10,1')->name('stock_audits.update-items');
        Route::get('debts', [\App\Http\Controllers\DebtController::class, 'index'])->name('debts.index');
        Route::get('debts/aging', [\App\Http\Controllers\DebtController::class, 'agingReport'])->name('debts.aging');
        Route::post('debts/{transaction}/payment', [\App\Http\Controllers\DebtController::class, 'storePayment'])->middleware('throttle:5,1')->name('debts.payment');

        // Sales Returns
        Route::get('/returns/create/{transaction}', [SalesReturnController::class, 'create'])->name('returns.create');
        Route::post('/returns/{return}/approve', [SalesReturnController::class, 'approve'])->middleware('throttle:10,1')->name('returns.approve');
        Route::post('/returns/{return}/complete', [SalesReturnController::class, 'complete'])->middleware('throttle:10,1')->name('returns.complete');
        Route::resource('returns', SalesReturnController::class)->only(['index', 'store', 'show'])->parameters(['returns' => 'return']);
        
        // AI Copilot
        Route::post('/ai/chat', [\App\Http\Controllers\OfflineAiController::class, 'chat'])->name('ai.chat')->middleware('throttle:30,1');
    });

    // 👑 OWNER, ADMIN, & MANAGER: Reports & Settings
    Route::middleware(['verified', 'role:owner,admin,admin_pusat,manager'])->group(function () {
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/sales/pdf', [ReportController::class, 'exportSales'])->name('sales.pdf');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/inventory/low-stock/pdf', [ReportController::class, 'exportLowStock'])->name('inventory.low-stock.pdf');
            Route::get('/inventory/expiry/pdf', [ReportController::class, 'exportExpiry'])->name('inventory.expiry.pdf');
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/customers', [ReportController::class, 'customerAnalytics'])->name('customers');
            Route::get('/export/sales', [ReportController::class, 'exportSales'])->name('export.sales');
            Route::get('/export/transactions', [ReportController::class, 'exportCsvTransactions'])->name('export.transactions');
            Route::get('/export/inventory', [ReportController::class, 'exportCsvInventory'])->name('export.inventory');

            // Excel Exports
            Route::get('/export/excel/sales', [ReportController::class, 'exportSalesExcel'])->name('export.excel.sales');
            Route::get('/export/excel/inventory', [ReportController::class, 'exportInventoryExcel'])->name('export.excel.inventory');
            Route::get('/export/excel/profit-loss', [ReportController::class, 'exportProfitLossExcel'])->name('export.excel.profit-loss');
        });

        // Settings (Sensitive)
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->middleware('throttle:10,1')->name('settings.update');
        Route::post('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup')->middleware('throttle:5,1');
        Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore')->middleware('throttle:5,1');

        // Payroll Management
        Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/generate', [\App\Http\Controllers\PayrollController::class, 'generate'])->middleware('throttle:5,1')->name('payroll.generate');

        // Commissions
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::post('/commissions/calculate', [CommissionController::class, 'calculate'])->middleware('throttle:5,1')->name('commissions.calculate');
        Route::post('/commissions/mark-paid', [CommissionController::class, 'markPaid'])->middleware('throttle:10,1')->name('commissions.mark-paid');
        
        // Employee Management
        Route::resource('employees', EmployeeController::class);
        Route::post('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->middleware('throttle:10,1')->name('employees.attendance');
        Route::post('/shifts/{shift}/review-photo', [\App\Http\Controllers\ShiftController::class, 'reviewPhoto'])->middleware('throttle:10,1')->name('shifts.review-photo');
    });

    // 📦 STOCK REQUESTS: Owner, Admin, Manager, Supervisor (view), Warehouse
    Route::middleware(['verified', 'role:owner,admin,admin_pusat,manager,supervisor,warehouse'])->group(function () {
        Route::get('/stock-requests', [StockRequestController::class, 'index'])->name('stock-requests.index');
        Route::get('/stock-requests/create', [StockRequestController::class, 'create'])->name('stock-requests.create');
        Route::get('/stock-requests/{stockRequest}', [StockRequestController::class, 'show'])->name('stock-requests.show');
        Route::post('stock-requests', [StockRequestController::class, 'store'])->middleware('throttle:10,1')->name('stock-requests.store');
        Route::patch('stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('stock-requests.approve');
        Route::patch('stock-requests/{stockRequest}/prepare', [StockRequestController::class, 'prepare'])->name('stock-requests.prepare');
        Route::patch('stock-requests/{stockRequest}/ship', [StockRequestController::class, 'ship'])->middleware('throttle:5,1')->name('stock-requests.ship');
        Route::patch('stock-requests/{stockRequest}/receive', [StockRequestController::class, 'receive'])->middleware('throttle:5,1')->name('stock-requests.receive');
        Route::patch('stock-requests/{stockRequest}/cancel', [StockRequestController::class, 'cancel'])->name('stock-requests.cancel');
    });

    // 👑 OWNER ONLY: Monitoring, Branch Management, Wholesale Customer Management
    Route::middleware(['verified', 'role:owner'])->group(function () {
        // Owner Monitoring
        Route::get('/owner/monitoring', [OwnerController::class, 'monitoring'])->name('owner.monitoring');
        Route::post('/owner/notifications/{id}/read', [OwnerController::class, 'markNotificationRead'])->name('owner.notifications.read');
        Route::post('/owner/notifications/read-all', [OwnerController::class, 'markAllNotificationsRead'])->name('owner.notifications.read-all');
        Route::get('/wholesale-customers', [\App\Http\Controllers\OwnerController::class, 'wholesaleCustomers'])->name('owner.wholesale-customers');
        Route::post('/owner/wholesale-customers/{id}/reset-password', [\App\Http\Controllers\OwnerController::class, 'resetWholesalePassword'])->name('owner.wholesale-reset-password');
        Route::post('/owner/wholesale-customers/{id}/update', [\App\Http\Controllers\OwnerController::class, 'updateWholesaleAccount'])->name('owner.wholesale-update');
        Route::get('/owner/wholesale-password-requests', [\App\Http\Controllers\OwnerController::class, 'wholesalePasswordRequests'])->name('owner.wholesale-password-requests');
        Route::post('/owner/wholesale-password-requests/{id}/resolve', [\App\Http\Controllers\OwnerController::class, 'resolveWholesalePasswordRequest'])->name('owner.wholesale-password-resolve');
        Route::get('/owner/wholesale-customers/{id}/orders', [\App\Http\Controllers\OwnerController::class, 'wholesaleCustomerOrders'])->name('owner.wholesale-customer-orders');
        Route::get('/owner/customer-accounts', [\App\Http\Controllers\OwnerController::class, 'customerAccounts'])->name('owner.customer-accounts');

        Route::resource('branches', BranchController::class);
        Route::get('/owner/special', [\App\Http\Controllers\OwnerController::class, 'specialPage'])->name('owner.special');
        Route::get('/owner/ai-dashboard', [\App\Http\Controllers\AiDashboardController::class, 'index'])->name('owner.ai-dashboard');

        // Loyalty System
        Route::prefix('owner/loyalty')->name('owner.loyalty.')->group(function () {
            Route::get('/', [\App\Http\Controllers\OwnerLoyaltyController::class, 'index'])->name('index');
            Route::get('/customer/{customer}', [\App\Http\Controllers\OwnerLoyaltyController::class, 'show'])->name('show');
            Route::post('/customer/{customer}/adjust', [\App\Http\Controllers\OwnerLoyaltyController::class, 'manualAdjust'])->name('adjust');
            Route::get('/redemptions', [\App\Http\Controllers\OwnerLoyaltyController::class, 'redemptions'])->name('redemptions');
            Route::post('/redemptions', [\App\Http\Controllers\OwnerLoyaltyController::class, 'storeRedemption'])->name('redemption.store');
            Route::post('/redemptions/{redemption}', [\App\Http\Controllers\OwnerLoyaltyController::class, 'updateRedemption'])->name('redemption.update');
            Route::get('/history', [\App\Http\Controllers\OwnerLoyaltyController::class, 'history'])->name('history');
        });
    });
});

// API Routes
Route::prefix('api')->middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/comparison', [DashboardController::class, 'comparison']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/inventory/alerts', [InventoryController::class, 'getAlerts']);
    Route::post('/customers', [CustomerController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::post('/ai/ask', [\App\Http\Controllers\AiAssistantController::class, 'ask'])->middleware('throttle:30,1');
});

require __DIR__.'/auth.php';

// ── Customer Portal (public, token-authenticated) ──
Route::prefix('portal')->middleware('throttle:30,1')->group(function () {
    Route::get('/{token}', [CustomerPortalController::class, 'dashboard'])->name('portal.dashboard');
    Route::get('/{token}/orders', [CustomerPortalController::class, 'orders'])->name('portal.orders');
    Route::get('/{token}/statement', [CustomerPortalController::class, 'statement'])->name('portal.statement');
});

// Public Invoice View for Customers
Route::get('/view-invoice/{invoice_number}', [TransactionController::class, 'publicInvoice'])
    ->middleware('throttle:10,1')
    ->name('transactions.public_invoice');

// ── Wholesale Customer Portal (login disediakan owner, tanpa registrasi mandiri) ──
Route::prefix('wholesale-customer')->name('wholesale.customer.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\WholesaleCustomerController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\WholesaleCustomerController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [\App\Http\Controllers\WholesaleCustomerController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('/forgot-password', [\App\Http\Controllers\WholesaleCustomerController::class, 'sendForgotPassword'])->middleware('throttle:3,10')->name('forgot-password.submit');
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\WholesaleCustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [\App\Http\Controllers\WholesaleCustomerController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [\App\Http\Controllers\WholesaleCustomerController::class, 'show'])->name('orders.show');
        Route::get('/history', [\App\Http\Controllers\WholesaleCustomerController::class, 'history'])->name('history');
        Route::get('/track', [\App\Http\Controllers\WholesaleCustomerController::class, 'trackOrder'])->name('track');
        Route::get('/leaderboard', [\App\Http\Controllers\WholesaleCustomerController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/loyalty', [\App\Http\Controllers\WholesaleCustomerController::class, 'loyalty'])->name('loyalty');
        Route::post('/redeem/{redemptionId}', [\App\Http\Controllers\WholesaleCustomerController::class, 'redeem'])->name('redeem');
        Route::post('/notifications/read-all', [\App\Http\Controllers\WholesaleCustomerController::class, 'markAllRead'])->name('notifications.read-all');
        Route::post('/logout', [\App\Http\Controllers\WholesaleCustomerController::class, 'logout'])->name('logout');
    });
});
// Customer Deposits
Route::resource('customer-deposits', App\Http\Controllers\CustomerDepositController::class);
Route::post('customer-deposits/{account}/transaction', [App\Http\Controllers\CustomerDepositController::class, 'transaction'])->name('customer-deposits.transaction');

// Expense Approvals
Route::get('expense-approvals', [App\Http\Controllers\ExpenseApprovalController::class, 'index'])->name('expense-approvals.index');
Route::post('expense-approvals/{approval}/approve', [App\Http\Controllers\ExpenseApprovalController::class, 'approve'])->name('expense-approvals.approve');
Route::post('expense-approvals/{approval}/reject', [App\Http\Controllers\ExpenseApprovalController::class, 'reject'])->name('expense-approvals.reject');

// Reports
Route::get('reports/daily-sales', App\Http\Controllers\DailySalesController::class)->name('reports.daily-sales');
Route::get('reports/stock-valuation', [App\Http\Controllers\StockValuationController::class, 'index'])->name('reports.stock-valuation');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{log}', [App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// Sales Targets
Route::resource('sales-targets', App\Http\Controllers\SalesTargetController::class);
