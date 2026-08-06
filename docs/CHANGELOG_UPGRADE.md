# APMS Upgrade Changelog — v2.0.0

## Overview
Complete enterprise upgrade covering 50 discrete commits across all 13 ERP audit categories.

## Security (5)
- SESSION_SECURE_COOKIE enabled for HTTPS-only sessions
- .env added to gitignore
- Health check endpoint for monitoring
- Activity logging viewer for audit trail
- API response transformer with standardized error handling

## Accounting Module (15) — NEW
- Chart of Accounts with 27 standard Indonesian accounts
- Accounting Periods with open/close lifecycle
- Double-entry Journal Entry system with balanced validation
- General Ledger with running balance
- Trial Balance report
- Income Statement (Laba Rugi)
- Balance Sheet (Neraca)
- Cash Flow Statement (Arus Kas)
- AutoPostingService for sales and expenses
- COA Seeder for initial setup

## Payroll & HR (5)
- BPJS Kesehatan & Ketenagakerjaan calculator
- PPh21 progressive tax calculator
- Overtime calculator with holiday rates
- Payroll deduction integration service
- Employee document management

## Business Features (10)
- Customer deposit accounts with transaction history
- Monthly sales targets with achievement tracking
- Supplier scorecard with on-time/quality metrics
- Reorder point calculator with 90-day averaging
- Promo engine with buy-X-get-Y and minimum purchase rules
- Expense approval workflow
- Payroll approval workflow
- Stock transfer approval workflow
- Stock valuation report
- Daily sales report with top products

## Data Management (5)
- Customer import from Excel
- Product import from Excel with auto-category creation
- Product export to Excel
- Customer export to Excel
- Transaction export to Excel with filters

## Performance (3)
- Composite database indexes on 6 high-traffic tables
- Cache warmup command
- PWA service worker for offline support

## Infrastructure (5)
- Database backup command
- Health check API endpoint
- Optimization configuration
- Version bump to 2.0.0
- Dead code cleanup

## Bug Fixes (2)
- Wholesale order branch scoping
- Dashboard COGS/avg_basket corrections

---

# Bug Fix Patch — v2.0.1

**Tanggal:** August 5, 2026

## Role & Authorization Fixes (Critical)

- **AppServiceProvider** — Perbaiki 30 Gate definitions: `admin_pusat` → `admin` di semua gate (role string tidak valid di `users.role` column)
- **AppServiceProvider** — Tambah 5 gate yang hilang: `transactions.view`, `transactions.create`, `stock_requests.create`, `stock_requests.approve`, `attendances.view`
- **AppServiceProvider** — Gate `wholesale.view`: perbaiki role string `'wholesale'` → `'wholesale_customer'`
- **AppServiceProvider** — Gate `manage_inventory`: tambah role `'warehouse'` yang terlewat
- **AppServiceProvider** — Notification filter: scope data sensitif (login activity, audit log, reset request) hanya untuk `owner` dan `admin`
- **routes/web.php** — Bersihkan 11 middleware `role:...,admin_pusat,...` di semua route group
- **routes/channels.php** — Perbaiki broadcast channel `inventory` dan `notifications`: hapus `admin_pusat` dan `admin_cabang` yang tidak pernah cocok dengan nilai `users.role`
- **WholesaleController** — Hapus referensi `admin_pusat`
- **ShiftController** — Hapus referensi `admin_pusat`
- **StockRequestController** — Hapus referensi `admin_pusat`
- **ReportController** — Hapus referensi `admin_pusat`
- **CashReconciliationController** — Hapus referensi `admin_pusat`
- **CustomerController** — Hapus referensi `admin_pusat`
- **TransactionController** — Hapus referensi `admin_pusat`
- **EmployeeController** — Hapus referensi `admin_pusat`
- **ExpensePolicy** — Hapus referensi `admin_pusat`
- **WholesaleOrderPolicy** — Hapus referensi `admin_pusat`
- **employees/create.blade.php**, **edit.blade.php**, **index.blade.php** — Hapus referensi `admin_pusat`
- **stock-requests/show.blade.php** — Hapus referensi `admin_pusat`

## Inventory / Stock Fixes

- **TransactionController**, **StoreTransactionRequest**, **SalesReturnController**, **StockAuditController**, **StockValuationController**, **PurchaseOrderController**, **ProductController**, **GoodsReceiptController** — Perbaiki `whereNull('branch_id')` untuk stok terpusat (branch_id = null)
- **transaction_details** — Tambah kolom `is_bonus_item` dan `bonus_ml` via migration `2026_08_05_014200`
- **SalesReturnController** / void logic — Bonus 20ml tersimpan dan di-restore dengan benar saat void transaksi

## Security

- **AI-INTEGRATION-GUIDE.md** — Hapus API key yang ter-expose dari dokumentasi, ganti dengan placeholder `YOUR_YUNWU_API_KEY_HERE`

## Docs Cleanup

- Hapus `docs/prompt-lengkap-nerva.md` (file tidak terkait APMS)
- Hapus `docs/documentation-metrics.md` (stale, tidak diupdate)
- Update `docs/ERP_SYSTEM_AUDIT.md` — reflect status bug fix
- Update `docs/reference/security/security-architecture.md` — sesuaikan dengan deployment aktual

## Running After Upgrade
```bash
php artisan migrate
php artisan db:seed --class=ChartOfAccountSeeder
php artisan cache:warmup
php artisan reorder:check
```
