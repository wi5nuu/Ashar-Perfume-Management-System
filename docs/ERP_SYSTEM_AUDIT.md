# ERP SYSTEM AUDIT — APMS (Ashar Parfum Management System)

**Audit Date:** July 13, 2026  
**Auditor:** Senior Enterprise Software Architect / ERP Consultant / Technical Auditor  
**Version:** 1.0 (Laravel 12, PHP 8.2+, MySQL 8.0)

---

## 1. Ringkasan Sistem

| Aspek | Detail |
|-------|--------|
| **Nama Sistem** | APMS — Ashar Parfum Management System |
| **Framework** | Laravel 12.x (MVC) |
| **Backend** | PHP 8.2+, MySQL 8.0+, Redis (optional) |
| **Frontend** | Blade + Bootstrap 4.6 + AdminLTE 3.2 + Alpine.js + Tailwind CSS 3 |
| **Database** | MySQL 8.0+ (InnoDB), SQLite (testing) |
| **Authentication** | Laravel Sanctum (token-based) + Session-based Web Auth |
| **Authorization** | RBAC (7 roles, 30+ permissions) + Policy-based |
| **API** | RESTful /api/v1 (Sanctum protected) |
| **Deployment** | cPanel shared hosting (Apache + MySQL), Vite build |
| **Architecture** | Monolithic Laravel MVC with Service Layer, Event-driven, Broadcast (Reverb) |
| **Jumlah Module** | 20+ functional modules |
| **Estimasi Kompleksitas** | **Enterprise-grade** — 48 Models, 106 Migrations, 70 Tables, 41 Controllers, 157+ Blade Views, 24 AI Tools, 9 Security Services |

---

## 2. Semua Modul yang Dimiliki

### 2.1 Point of Sale (POS) — ✅ Lengkap
- **Fungsi:** Transaksi penjualan eceran dengan multi-payment, diskon, kupon, pajak, hutang
- **Status:** Production-ready
- **Fitur:** Cash/QRIS/Transfer/E-Wallet/Debit/Credit card, diskon item/global, PPN toggle, bon/kasbon, struk digital, barcode scanning (html5-qrcode), multi-window POS
- **Kekurangan:** Tidak ada edit transaksi (hanya void/return), tidak ada offline mode POS

### 2.2 Wholesale / B2B Grosir — ✅ Lengkap
- **Fungsi:** Manajemen pesanan grosir dari pelanggan B2B
- **Status:** Production-ready
- **Fitur:** Order lifecycle (pending→reviewed→on_progress→packed→shipped→delivered→completed), tracking number, shipping cost, packing, customer portal, referral program, loyalty points + 15-tier rank system, katalog produk grosir
- **Kekurangan:** Tidak ada integrasi marketplace (Tokopedia, Shopee), tidak ada dropship mode

### 2.3 Inventory Management — ✅ Lengkap
- **Fungsi:** Manajemen stok multi-cabang stok, gudang, adjustment, audit, transfer
- **Status:** Production-ready
- **Fitur:** Multi-warehouse, stock adjustment (add/subtract/set), stock opname (audit), inventory movements (riwayat), minimum stock alerts, expiry date tracking, refill system (ml), bulk stock, batch number, FIFO tracking via purchase_price, barcode integration
- **Kekurangan:** Tidak ada FIFO/LIFO otomatis (manual COGS dari purchase_price), tidak ada reorder point otomatis

### 2.4 Product Management — ✅ Lengkap
- **Fungsi:** Manajemen produk multi-kategori dengan pricing tier
- **Status:** Production-ready
- **Fitur:** Multi-kategori, barcode, multiple pricing (retail/wholesale), refill product (per ml), image upload, supplier linkage, price history tracking, bulk price update, minimum stock per product
- **Kekurangan:** Tidak ada variant/option (size, warna), tidak ada bundle/paket hemat, tidak ada recipe/BOM

### 2.5 Customer Management (CRM) — 🟡 Cukup
- **Fungsi:** Manajemen data pelanggan
- **Status:** Production-ready
- **Fitur:** Data pelanggan lengkap (nik, gender, birth, loyalty rank), debt tracking, customer portal, points, segmentasi (retail/wholesale), spending analytics, origin tracking (geographic), aroma preferences
- **Kekurangan:** Tidak ada marketing automation (email blast, promo broadcast), tidak ada customer segment dynamic, tidak ada sales funnel, tidak ada support ticket

### 2.6 Supplier & Purchase — ✅ Lengkap
- **Fungsi:** Manajemen supplier dan purchase order
- **Status:** Production-ready
- **Fitur:** Supplier management, supplier pricing per product, purchase order (draft→sent→received→cancelled), goods receipt, PO status tracking, expected arrival
- **Kekurangan:** Tidak ada evaluasi supplier otomatis (scorecard), tidak ada drop-ship PO

### 2.7 Stock Request (Branch → Warehouse) — ✅ Lengkap
- **Fungsi:** Permintaan stok dari cabang ke gudang pusat
- **Status:** Production-ready
- **Fitur:** Request lifecycle (pending→approved→preparing→shipped→received→cancelled), approval workflow, quantity tracking per status, delivery tracking
- **Kekurangan:** Tidak terintegasi otomatis dengan Purchase Order

### 2.8 Sales Return — ✅ Lengkap
- **Fungsi:** Retur penjualan
- **Status:** Production-ready
- **Fitur:** Return number, reason, status (pending→approved→completed→rejected), refund tracking, stock restoration on approval
- **Kekurangan:** Retur pembelian belum ada (hanya retur penjualan)

### 2.9 Debt / Piutang — ✅ Lengkap
- **Fungsi:** Manajemen hutang piutang pelanggan
- **Status:** Production-ready
- **Fitur:** Debt recording, payment collection, debt aging report, payment history, due reminders (broadcast event)
- **Kekurangan:** Tidak ada invoice formal, tidak ada denda keterlambatan

### 2.10 Expense Management — ✅ Lengkap
- **Fungsi:** Pencatatan biaya operasional
- **Status:** Production-ready
- **Fitur:** Multi-kategori biaya, proof image upload, recurring/one-time, vendor tracking, daily expense tracking
- **Kekurangan:** Tidak ada budget vs actual, tidak ada approval workflow

### 2.11 Employee / HR — ✅ Lengkap
- **Fungsi:** Manajemen data karyawan
- **Status:** Production-ready
- **Fitur:** Data pribadi, dokumen (NIK, NPWP, bank), status pekerjaan, riwayat jabatan, emergency contact, housing tracking (mess), skills, full corporate fields
- **Kekurangan:** Tidak ada cuti/sakit/ijin tracking, tidak ada kontrak management

### 2.12 Attendance — ✅ Lengkap
- **Fungsi:** Absensi karyawan
- **Status:** Production-ready
- **Fitur:** Daily attendance, time in/out, status (present/sick/leave/absent), role tracking, auto-attendance on login for cashier
- **Kekurangan:** Tidak ada GPS/lokasi, tidak ada foto selfie check-in, tidak ada fingerprint/integrasi biometric, tidak ada overtime auto-calc

### 2.13 Shift Management — ✅ Lengkap
- **Fungsi:** Manajemen shift kasir dengan cash reconciliation
- **Status:** Production-ready
- **Fitur:** Open/close shift, initial cash, expected vs actual cash, discrepancy, closing photo, cash breakdown & denominations, supervisor review, manager notes
- **Kekurangan:** Tidak ada shift scheduling otomatis

### 2.14 Payroll — 🟡 Basic
- **Fungsi:** Penggajian karyawan
- **Status:** Basic implementation (hanya index)
- **Fitur:** Basic salary, allowances, deductions, payroll settings per user, status (pending/paid)
- **Kekurangan:** **Tidak ada create/edit/show** untuk payroll, tidak ada integrasi attendance→payroll, tidak ada BPJS/PPh 21, tidak ada slip gaji, tidak ada THR, tidak ada lembur otomatis

### 2.15 Commission — ✅ Lengkap
- **Fungsi:** Komisi karyawan per transaksi
- **Status:** Production-ready
- **Fitur:** Commission rate per transaction, month grouping, status (pending/paid), per-user tracking
- **Kekurangan:** Tidak ada komisi tim/sharing, tidak ada target-based commission

### 2.16 Coupon / Promo — ✅ Lengkap
- **Fungsi:** Kupon diskon
- **Status:** Production-ready
- **Fitur:** Multi-type (discount/bonus/cashback), percentage/nominal, max usage, per-customer, expiration
- **Kekurangan:** Tidak ada buy-one-get-one, tidak ada promo bundle, tidak ada promo bersyarat kompleks

### 2.17 Reports & Analytics — ✅ Lengkap
- **Fungsi:** Laporan penjualan, inventory, laba rugi, customer analytics
- **Status:** Production-ready
- **Fitur:** Sales report (daily/monthly), P&L (COGS, gross profit, net), inventory (low stock, out of stock, expiring), customer analytics (top customers, growth, types), branch comparison, payment distribution, multi-period chart, smart insights
- **Kekurangan:** Tidak ada balance sheet, tidak ada cash flow statement, tidak ada custom report builder

### 2.18 Export (PDF/CSV/Excel) — ✅ Lengkap
- **Fungsi:** Ekspor laporan ke berbagai format
- **Status:** Production-ready
- **Fitur:** PDF (sales, inventory, P&L), CSV (transactions, inventory), Excel (sales, inventory, P&L) via maatwebsite/Laravel Excel
- **Kekurangan:** Tidak ada scheduled auto-export

### 2.19 AI Copilot — ✅ Advanced
- **Fungsi:** Asisten AI untuk query bisnis
- **Status:** Production-ready
- **Fitur:** Claude Haiku API (primary), Rule-based fallback (offline), 24 analytical tools, 16 intent handlers, 45+ knowledge base entries, strategic business advice, anomaly detection, business health score, unanswered query logging
- **Kekurangan:** Tidak ada training/fine-tuning, tidak ada multi-bahasa (hanya Indonesia)

### 2.20 Security & Admin — ✅ Advanced
- **Fungsi:** Keamanan sistem dan administrasi
- **Status:** Production-ready
- **Fitur:** RBAC (7 roles, 30+ permissions), 2FA (custom TOTP), audit trail, IP blacklist, login monitoring, session security, password policies (history, strength, expiry), file upload security, POS anti-tampering, data integrity (HMAC), field encryption, backup (SQL + file), security headers, CSP, rate limiting, IP whitelist
- **Kekurangan:** No SSO/LDAP, no OAuth2 provider, no SIEM integration

### 2.21 Multi-Branch — ✅ Lengkap
- **Fungsi:** Manajemen multi-cabang
- **Status:** Production-ready
- **Fitur:** Branch creation, branch-scoped data isolation, per-branch KPIs, branch comparison reports, warehouse per branch, operational hours
- **Kekurangan:** Tidak ada inter-cabang transaksi, tidak ada consolidated GL

### 2.22 Dashboard — ✅ Lengkap
- **Fungsi:** Dashboard real-time dengan KPI
- **Status:** Production-ready
- **Fitur:** Real-time via Reverb WebSocket, period comparison (MoM/YoY), sales chart, payment distribution, recent transactions, stock alerts, top products, financial summary, smart insights, daily stats (retail/grosir sales, transactions, stock requests, expenses), branch revenue (owner)
- **Kekurangan:** Tidak ada customizable dashboard/widget

### 2.23 Owner Dashboard — ✅ Lengkap
- **Fungsi:** Dashboard khusus owner
- **Status:** Production-ready
- **Fitur:** AI strategic dashboard, wholesale customer management, loyalty management, password reset requests, monitoring, branch comparison
- **Kekurangan:** Tidak ada real-time business health monitor

### 2.24 Customer Portal (Wholesale) — ✅ Lengkap
- **Fungsi:** Portal self-service pelanggan grosir
- **Status:** Production-ready
- **Fitur:** Order tracking, order history, loyalty program, referral system, points/credits management, leaderboard, Google OAuth login
- **Kekurangan:** Tidak ada self-service order creation, tidak ada chat/support

### 2.25 Loyalty Program — ✅ Advanced
- **Fungsi:** Program loyalitas pelanggan grosir
- **Status:** Production-ready
- **Fitur:** 15-tier rank system (exponential scaling), credit-based (1 credit = Rp 3,333 spend), referral rewards, redemption catalog, rank-based benefits (free shipping, priority, discount up to 50%)
- **Kekurangan:** Loyalitas retail masih basic (points only)

---

## 3. Audit Feature ERP

| Feature | Ada | Keterangan |
|---------|:---:|-----------|
| **Kasir POS** | ✅ | Multi-payment, diskon, kupon, hutang, barcode scan, multi-window |
| **Barcode** | ✅ | Produk barcode, scanning di POS, cetak barcode label |
| **QR Code** | ✅ | Invoice QR, 2FA QR, order tracking QR |
| **Produk** | ✅ | Multi-kategori, pricing tier, refill, image, supplier linkage |
| **Kategori** | ✅ | product_categories + categories table |
| **Varian** | ❌ | Belum ada (size hanya string field, bukan variant system) |
| **Bundle** | ❌ | Belum ada bundle/paket produk |
| **Multi Satuan** | 🟡 | Partial — wholesale punya pieces_per_unit, retail belum |
| **Stock** | ✅ | Multi-cabang, multi-gudang, minimum stock, batch, expiry |
| **Gudang** | ✅ | Warehouses dengan branch linkage |
| **Multi Gudang** | ✅ | Multiple warehouses per branch |
| **Transfer Gudang** | 🟡 | Hanya via Stock Request, belum ada transfer langsung antar gudang |
| **Stock Adjustment** | ✅ | Add/subtract/set dengan audit trail |
| **Stock Opname** | ✅ | Stock audit dengan perbandingan fisik vs sistem |
| **Purchase** | ✅ | Purchase order full lifecycle |
| **Purchase Order** | ✅ | Draft→sent→received→cancelled, partial receive, expected date |
| **Sales Order** | 🟡 | Hanya untuk grosir (WholesaleOrder), retail langsung POS |
| **Quotation** | ❌ | Belum ada |
| **Invoice** | 🟡 | Invoice number di transaksi, tapi bukan formal invoicing system |
| **Return Penjualan** | ✅ | Full lifecycle dengan refund tracking |
| **Return Pembelian** | ❌ | Belum ada |
| **CRM** | 🟡 | Data pelanggan + portal, tidak ada marketing automation |
| **Customer** | ✅ | Full profile dengan segmentasi |
| **Supplier** | ✅ | Supplier management + pricing |
| **Membership** | 🟡 | Loyalty points untuk retail, credit+rank untuk grosir |
| **Deposit Customer** | ❌ | Belum ada deposit/prepaid |
| **Loyalty** | ✅ | Advanced wholesale loyalty (15 tiers), basic retail points |
| **Accounting** | ❌ | **Tidak ada** — no chart of account, no journal, no GL |
| **Chart of Account** | ❌ | Belum ada |
| **Journal** | ❌ | Belum ada |
| **General Ledger** | ❌ | Belum ada |
| **Balance Sheet** | ❌ | Belum ada |
| **Profit Loss** | ✅ | Revenue - COGS - Expenses, branch-scoped |
| **Cash Flow** | ❌ | Belum ada |
| **Auto Posting** | ❌ | Tidak ada double-entry accounting |
| **Tax** | 🟡 | PPN toggle (10%) di transaksi, tidak ada PPh, tidak ada laporan pajak |
| **Payroll** | 🟡 | Basic — hanya index, belum ada create/edit/slip/BPJS |
| **Attendance** | ✅ | Time in/out dengan status, role tracking |
| **Shift** | ✅ | Cash reconciliation, photo, denominations |
| **Employee** | ✅ | Full HR data, corporate fields |
| **Salary** | 🟡 | Basic salary + allowances + deductions, tanpa integrasi attendance |
| **Commission** | ✅ | Per-transaksi, multi-status |
| **Production** | ❌ | Tidak ada |
| **Bill of Material** | ❌ | Tidak ada |
| **Manufacturing** | ❌ | Tidak ada |
| **Production Order** | ❌ | Tidak ada |
| **Recipe** | 🟡 | Refill product (ml-based) bisa dianggap minuman isi ulang |
| **Inventory Cost** | 🟡 | cost_per_unit di inventory, tapi tidak ada average/fifo automasi |
| **COGS** | ✅ | Dihitung dari purchase_price * quantity di transaction_details |
| **FIFO** | ❌ | Tidak ada implementasi FIFO otomatis |
| **Average Cost** | ❌ | Tidak ada weighted average cost |
| **Notification** | ✅ | Database + WebSocket + Email |
| **Email** | ✅ | Daily/weekly report, login notification, password reset |
| **WhatsApp** | 🟡 | WhatsApp link generation (manual, no API integration) |
| **SMS** | ❌ | Belum ada |
| **Push Notification** | ❌ | Tidak ada (hanya in-app via WebSocket) |
| **Dashboard** | ✅ | Real-time, period comparison, KPI cards, charts |
| **Analytics** | ✅ | AI-powered analytics, trend detection, anomaly detection |
| **Reports** | ✅ | Sales, P&L, Inventory, Customer, Export PDF/CSV/Excel |
| **Export Excel** | ✅ | Via maatwebsite/Laravel Excel |
| **Export PDF** | ✅ | Via DomPDF |
| **Import Excel** | ❌ | Belum ada |
| **API** | ✅ | RESTful v1 via Sanctum |
| **Webhook** | ❌ | Belum ada |
| **Role Permission** | ✅ | RBAC dengan 7 roles + 30+ permissions, direct user permissions |
| **Audit Log** | ✅ | Model events, login activity, integrity check |
| **Backup** | ✅ | SQL (3 schedules) + full system (Spatie) + AES-256 encryption |
| **Restore** | ✅ | Via artisan command |
| **Scheduler** | ✅ | 11 scheduled tasks (backup, report, cleanup, monitoring) |
| **Queue** | ✅ | Jobs for report generation, low stock check, email |
| **AI Feature** | ✅ | Claude Haiku + Rule-based engine, 24 tools, strategic advice |
| **Automation** | 🟡 | Scheduled reports, auto-backup, password expiry, low stock alerts |
| **Marketplace Integration** | ❌ | Tidak ada |
| **Payment Gateway** | 🟡 | QRIS (manual), E-Wallet (manual), Transfer (manual) |
| **QRIS** | 🟡 | Payment method option, no automatic QRIS gateway |
| **Mobile Friendly** | ✅ | Mobile bottom nav, touch optimization, safe areas, responsive |
| **PWA** | 🟡 | Manifest exists, SW exists but NOT registered |
| **Offline Mode** | ❌ | Tidak ada |
| **Realtime** | ✅ | WebSocket via Laravel Reverb, 6 broadcast events |

---

## 4. Audit Database

| Aspek | Detail |
|-------|--------|
| **Jumlah Table** | 70 tables (60 business + 10 framework) |
| **Jumlah Migration** | 106 migration files |
| **Relasi Database** | 90 foreign keys — well-relational |
| **Normalization** | Mostly 3NF — inventory (product_id, branch_id) unique composite |
| **Soft Delete** | 6 tables: users, transactions, expenses, wholesale_orders, debt_payments, wholesale_products |
| **Index** | 60+ indexes including composite indexes for perf |
| **Foreign Key** | 90 foreign keys with proper ON DELETE (CASCADE/SET NULL/RESTRICT) |
| **Potential Bottleneck** | `audit_logs` bisa besar (tanpa partitioning), `inventory_movements` tumbuh cepat, `transaction_details` tanpa index composite |

### Anomalies Ditemukan
| Issue | Detail |
|-------|--------|
| `idx_coupon_status` | Mengacu kolom `status` yang tidak ada di tabel `coupons` (hanya `is_active`) |
| `idx_dp_customer` | Mengacu kolom `customer_id` yang tidak ada di tabel `debt_payments` |
| `commission` indexes | Nama tabel `commission` vs aktual `commissions` (plural) |
| `stock_forecasts` | Table exists but NO Model — tidak terpakai |
| `shift_audits` | Table exists but NO Model — tidak terpakai |
| `customer_segments` | Table exists but NO Model — tidak terpakai |

---

## 5. Audit Security

| Aspek | Status | Detail |
|-------|--------|--------|
| **Authentication** | ✅ | Session + Sanctum API tokens, 2 custom flows |
| **Authorization** | ✅ | RBAC (7 roles) + 30+ permissions + Gates + Policies |
| **CSRF** | ✅ | Laravel VerifyCsrfToken + Sanctum CSRF |
| **XSS** | 🟡 | Blade auto-escaping ✅, InputSanitizer (strip_tags) lemah, CSP partial (unsafe-inline style) |
| **SQL Injection** | ✅ | Eloquent parameterized queries, raw queries use binding |
| **Rate Limit** | ✅ | Login: 5/15min (double: custom + Laravel), API: 60-120/min, IP: 200/min |
| **Encryption** | ✅ | AES-256-GCM field-level (Crypt), AES-256-CBC backup |
| **Password Hash** | ✅ | Bcrypt 12 rounds |
| **Session Security** | ✅ | Encrypted, HttpOnly, SameSite=Strict, 120min lifetime |
| **JWT** | 🟡 | Tidak ada JWT, pakai Sanctum token |
| **Cookie Security** | 🟡 | `SESSION_SECURE_COOKIE=false` di .env — **TIDAK AMAN** |
| **Audit Log** | ✅ | Model CRUD events, login activity, file upload, POS |
| **Backup** | ✅ | Multi-schedule, encrypted, monitored |

### Critical Vulnerabilities
1. **`.env` file committed to repository** — APP_KEY, DB_PASSWORD, REVERB secrets exposed
2. **`SESSION_SECURE_COOKIE=false`** — session cookie dikirim via HTTP
3. **Custom TOTP** — tidak ada rate limiting di 2FA, tidak ada recovery codes
4. **`EncryptionService::decryptField()`** — mengembalikan plaintext jika dekripsi gagal
5. **Dual authorization system** — RBAC + role-string gates bisa inkonsisten
6. **KnownDevice model never used** — login notification for new device tidak aktif
7. **Password reset stores password encrypted (not hashed)** — admin bisa lihat password user

---

## 6. Audit Performance

| Aspek | Status | Detail |
|-------|--------|--------|
| **Caching** | ✅ | Cache dashboard KPIs (30-3600s), permission cache (3600s), payment data |
| **Lazy Loading** | ✅ | `with()` eager loading, relation lazy loading where appropriate |
| **Database Optimization** | 🟡 | 60+ indexes, composite indexes, tapi ada index anomali (2 index mengacu kolom salah) |
| **Queue** | ✅ | Jobs for report, low stock check, email — database driver |
| **Background Job** | ✅ | 5 jobs + scheduler with 11 tasks |
| **Realtime** | ✅ | WebSocket via Reverb, 6 events, 4 private channels |
| **Pagination** | ✅ | All list views paginated (10-20 per page) |
| **N+1 Query** | 🟡 | Beberapa controller tidak eager-load relations |
| **File Upload** | ✅ | Max 5MB, MIME validation, image integrity check |
| **Storage** | ✅ | Local disk (public), backup to storage/app/backups |

---

## 7. Audit Code Quality

| Aspek | Status | Detail |
|-------|--------|--------|
| **Architecture** | 🟡 | Monolithic Laravel — service layer, event-driven, broadcast |
| **Folder Structure** | ✅ | Standard Laravel structure, well-organized modules |
| **SOLID** | 🟡 | Service layer ✓, DI ✓, tapi Controller terlalu gemuk (500+ lines beberapa) |
| **Repository Pattern** | ❌ | Tidak ada — query langsung dari controller atau model |
| **Service Pattern** | ✅ | 6 core services + 9 security services + 17 intent handlers |
| **Reusable Component** | ✅ | Blade components (13), traits (2), form requests (8) |
| **Clean Code** | 🟡 | Mixed — ada yang rapi, ada controller 1500+ lines (AiCopilotService) |
| **Maintainability** | 🟡 | Modular tapi beberapa file terlalu besar |
| **Scalability** | 🟡 | Monolithic — perlu microservices untuk scale horizontal |

---

## 8. Fitur yang Setara Dengan Majoo

### Majoo Starter
| Fitur | Status |
|-------|--------|
| POS Kasir | ✅ Lebih Baik (multi-payment, barcode scan, multi-window) |
| Manajemen Stok | ✅ Lebih Baik (multi-gudang, batch, expiry) |
| Laporan Penjualan | ✅ Lebih Baik (AI-powered analytics) |
| Manajemen Produk | ✅ Setara |
| Manajemen Pelanggan | ✅ Lebih Baik (portal, loyalty, referral) |
| Multi Cabang | ✅ Lebih Baik (data isolation per cabang) |
| Backup Otomatis | ✅ Lebih Baik (AES-256 encrypted) |

### Majoo Advance
| Fitur | Status |
|-------|--------|
| Manajemen Piutang | ✅ Setara |
| Manajemen Hutang | 🟨 Setara (debt tracking + aging) |
| Purchasing | ✅ Setara (PO + goods receipt) |
| Manajemen Gudang | ✅ Setara (multi-warehouse) |
| CRM | 🟨 Setara (customer portal, loyalty) |
| Karyawan & Absensi | ✅ Lebih Baik (shift + cash reconciliation) |
| Laporan Keuangan | ❌ **Majoo lebih unggul** (APMS tidak punya accounting) |
| Manajemen Aset | ❌ Belum Ada |
| Integrasi Marketplace | ❌ Belum Ada |

### Majoo Prime
| Fitur | Status |
|-------|--------|
| Akuntansi (COA, Jurnal, GL) | ❌ **Belum Ada** — gap terbesar |
| Laporan Pajak (PPN, PPh) | ❌ Belum Ada |
| Payroll Lengkap | ❌ **Belum Ada** (APMS masih basic) |
| Manajemen Produksi | ❌ Belum Ada |
| CRM Marketing | ❌ Belum Ada |
| AI & Otomatisasi | ✅ **APMS Lebih Baik** (AI Copilot, strategic advice, anomaly detection) |
| Dashboard Real-time | ✅ **APMS Lebih Baik** (WebSocket real-time, period comparison) |
| Loyalty Program | ✅ **APMS Lebih Baik** (15 tiers, credit system, referral) |
| Keamanan | ✅ **APMS Lebih Baik** (2FA, field encryption, audit trail, CSP, rate limiting) |
| Fleksibilitas Role | ✅ **APMS Lebih Baik** (RBAC granular, 7 roles, 30+ permissions) |

---

## 9. Fitur yang Tidak Dimiliki Majoo Tetapi Dimiliki Sistem Ini

| Fitur | Deskripsi |
|-------|-----------|
| **AI Copilot** | Claude Haiku + rule-based engine untuk business query, 24 tools analitik |
| **AI Strategic Advisor** | Rekomendasi bisnis otomatis (13 jenis: cross-sell, pricing, stock, etc.) |
| **Business Health Score** | 5-pilar score (profitability, stock, growth, liquidity, efficiency) |
| **Anomaly Detection** | Deteksi otomatis sales drop, expense spike, stock issues, missing shifts |
| **Wholesale Loyalty 15 Tiers** | Sistem rank eksponensial (Regular → Infinite) dengan credit/reward |
| **Referral Program** | Kode referral untuk pelanggan grosir |
| **Customer Origin Tracking** | Analisis geografis pelanggan dari alamat |
| **Peak Hours Analysis** | Analisis jam sibuk bisnis |
| **Real-time Dashboard via WebSocket** | Update KPI langsung tanpa refresh |
| **POS Anti-Tampering** | Deteksi manipulasi harga di POS (tolerance Rp 100) |
| **Data Integrity HMAC** | Checksum SHA-256 untuk verifikasi integritas transaksi |
| **Shift Cash Reconciliation** | Foto closing shift, breakdown denominations, supervisor review |
| **Field-level Encryption** | Enkripsi NIK, NPWP, bank account, 2FA secret |
| **Security Headers** | CSP nonce, HSTS, X-Frame-Options, Permissions-Policy |
| **Refill System** | Produk isi ulang dengan tracking volume (ml) |
| **Stock Request Pipeline** | Permintaan stok cabang→gudang dengan approval |
| **Supplier Price History** | Tracking harga supplier per produk |
| **Commission Engine** | Komisi per transaksi dengan multi-status |

---

## 10. Fitur Majoo Yang Belum Dimiliki Sistem Ini

### Critical Gaps
| Fitur | Prioritas | Notes |
|-------|-----------|-------|
| **Chart of Account + Jurnal + GL** | Critical | Double-entry accounting |
| **Balance Sheet** | Critical | Laporan posisi keuangan |
| **Cash Flow Statement** | Critical | Laporan arus kas |
| **Auto Posting** | Critical | Posting otomatis ke GL dari transaksi |
| **Laporan Pajak (PPN, PPh 21/23/4a2)** | Critical | Fitur wajib pajak |
| **Payroll Lengkap (slip, BPJS, THR)** | Critical | Hitung PPh 21, BPJS, THR |

### High Priority
| Fitur | Prioritas |
|-------|-----------|
| Integrasi Payment Gateway (Midtrans/Xendit) | High |
| Marketplace Integration (Shopee, Tokopedia) | High |
| Import Excel (produk, pelanggan) | High |
| Varian Produk (size, warna, grade) | High |
| Bundle/Paket Produk | High |
| Quotation / Proforma Invoice | High |
| CRM Marketing (email blast, promo) | High |
| Manajemen Aset | High |
| Approval Workflow Engine | High |
| Custom Report Builder | High |

### Medium Priority
| Fitur | Prioritas |
|-------|-----------|
| Webhook (inbound/outbound) | Medium |
| Deposit Customer / Prepaid | Medium |
| Multi Satuan Produk | Medium |
| FIFO/LIFO Cost Method | Medium |
| Reorder Point Otomatis | Medium |
| Sales Target & Forecasting | Medium |
| Employee Leave Management | Medium |
| Overtime Auto Calculation | Medium |
| GPS Attendance | Medium |
| Purchase Return | Medium |
| Dropship Mode (PO langsung ke customer) | Medium |
| SSO / LDAP / OAuth Provider | Medium |
| Mobile Push Notification (FCM) | Medium |

### Low Priority
| Fitur | Prioritas |
|-------|-----------|
| PWA Full (SW register + offline fallback) | Low |
| Offline Mode (localStorage sync) | Low |
| Multi Currency | Low |
| E-Wallet Internal | Low |
| Marketplace sebagai Seller | Low |
| Production / BOM / Manufacturing | Low |
| Time Tracking (project-based) | Low |
| Document Management | Low |
| Chat/Support Ticketing | Low |
| Scheduled Auto-Export | Low |

---

## 11. Skor ERP

| Modul | Skor | Alasan |
|-------|:----:|--------|
| **POS** | 8.5/10 | Sangat lengkap, multi-payment, barcode, kupon. Kurang: offline mode, edit transaksi |
| **Inventory** | 8.0/10 | Multi-gudang, audit trail, expiry, batch. Kurang: FIFO/LIFO auto, reorder point |
| **Accounting** | 1.5/10 | **GAP terbesar.** Tidak ada double-entry, COA, GL, balance sheet, cash flow |
| **CRM** | 5.5/10 | Portal customer, loyalty. Tapi tidak ada marketing automation, segment dynamic, funnel |
| **Production** | 1.0/10 | Hanya refill system (ml-based). Tidak ada BOM, manufacturing, production order |
| **Security** | 9.0/10 | Sangat kuat: RBAC, 2FA, audit, encryption, CSP, rate limit, anti-tampering |
| **Performance** | 7.0/10 | Caching baik, queue, pagination. Tapi monolith, perlu optimalisasi query |
| **Architecture** | 7.5/10 | Service layer, event-driven, broadcast. Tapi controller gemuk, no repository |
| **UX** | 7.5/10 | Mobile friendly, real-time, responsive. Tapi AdminLTE 3 agak outdated |
| **Scalability** | 5.0/10 | Monolithic Laravel — perlu microservices atau modular monolith untuk scale |

### Overall Score: **6.3/10**

---

## 12. Kesimpulan

### Perbandingan dengan Majoo

| Aspek | Majoo Starter | Majoo Advance | Majoo Prime |
|-------|:-------------:|:-------------:|:-----------:|
| **POS** | ✅ **APMS Unggul** | 🟨 Setara | 🟨 Setara |
| **Inventory** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | 🟨 Setara |
| **Accounting** | ❌ Majoo Unggul | ❌ Majoo Unggul | ❌ Majoo Unggul |
| **CRM** | ✅ **APMS Unggul** | 🟨 Setara | 🟨 Setara |
| **Payroll** | ❌ Majoo Unggul | ❌ Majoo Unggul | ❌ Majoo Unggul |
| **AI/Analytics** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | ✅ **APMS Unggul** |
| **Security** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | ✅ **APMS Unggul** |
| **Loyalty** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | ✅ **APMS Unggul** |
| **Laporan Keuangan** | ❌ Majoo Unggul | ❌ Majoo Unggul | ❌ Majoo Unggul |
| **Marketplace** | ❌ Majoo Unggul | ❌ Majoo Unggul | ❌ Majoo Unggul |
| **Multi Cabang** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | 🟨 Setara |
| **Realtime** | ✅ **APMS Unggul** | ✅ **APMS Unggul** | ✅ **APMS Unggul** |

### Kesimpulan Akhir

**APMS unggul dari Majoo Starter** di hampir semua aspek — POS lebih modern, inventory multi-gudang, AI analytics, security enterprise-grade, real-time dashboard, dan loyalty system.

**APMS setara dengan Majoo Advance** — sama-sama kuat di POS, inventory, purchasing. APMS unggul di AI, security, loyalty. Majoo unggul di accounting.

**APMS belum bisa menyaingi Majoo Prime** untuk enterprise yang membutuhkan double-entry accounting, laporan pajak, payroll enterprise, dan integrasi marketplace.

**Strategi:** Jika APMS menambahkan modul Accounting (COA, Jurnal, GL, Balance Sheet, Cash Flow) dan payroll lengkap dalam 6 bulan ke depan, APMS bisa menjadi alternatif yang **lebih unggul dari Majoo Prime** untuk UKM/retail di Indonesia, terutama dengan keunggulan AI, security, dan real-time yang tidak dimiliki Majoo.

---

## 13. Roadmap — 50 Fitur Prioritas

### Critical (Harus dalam 3 Bulan)

| # | Fitur | Module | Estimasi |
|---|-------|--------|----------|
| 1 | **Chart of Account** — COA dengan kode akun standar | Accounting | 2 minggu |
| 2 | **Double-Entry Journal** — Jurnal otomatis dari semua transaksi | Accounting | 3 minggu |
| 3 | **General Ledger** — GL dengan saldo akun real-time | Accounting | 2 minggu |
| 4 | **Trial Balance** — Neraca saldo sebelum laporan keuangan | Accounting | 1 minggu |
| 5 | **Balance Sheet** — Laporan posisi keuangan (aset, liabilitas, ekuitas) | Accounting | 1 minggu |
| 6 | **Income Statement** — Laba rugi formal (dari GL, bukan query langsung) | Accounting | 1 minggu |
| 7 | **Cash Flow Statement** — Laporan arus kas (metode langsung/tidak langsung) | Accounting | 2 minggu |
| 8 | **Auto Posting Engine** — Posting otomatis dari POS, Purchase, Expense ke GL | Accounting | 4 minggu |
| 9 | **Account Payable (Hutang)** — Manajemen hutang usaha formal | Accounting | 2 minggu |
| 10 | **Account Receivable (Piutang)** — Piutang formal dengan invoice | Accounting | 2 minggu |
| 11 | **Payroll Lengkap** — Slip gaji, PPh 21, BPJS, THR, lembur | HR | 4 minggu |
| 12 | **Laporan PPN** — PPN Masukan/Keluaran, SPT Masa PPN | Tax | 2 minggu |
| 13 | **Laporan PPh 21** — Hitung PPh 21 karyawan | Tax | 2 minggu |
| 14 | **Payment Gateway** — Integrasi Midtrans/Xendit (QRIS, VA, CC) | POS | 3 minggu |
| 15 | **Varian Produk** — Size, warna, grade dengan stock per variant | Product | 3 minggu |

### High (3-6 Bulan)

| # | Fitur | Module | Estimasi |
|---|-------|--------|----------|
| 16 | **Import Excel** — Produk, pelanggan, inventory massal | Product | 1 minggu |
| 17 | **Bundle/Paket Produk** — Paket hemat dengan harga khusus | Product | 2 minggu |
| 18 | **Multi Satuan** — Satuan berbeda per produk (pcs, box, dus, lusin) | Product | 2 minggu |
| 19 | **Integrasi Marketplace** — Shopee, Tokopedia, Lazada API | Sales | 6 minggu |
| 20 | **Quotation / Proforma** — Buat dan kirim penawaran ke customer | Sales | 2 minggu |
| 21 | **Approval Workflow** — Engine untuk approval multi-level | System | 3 minggu |
| 22 | **CRM Marketing** — Email blast, promo broadcast, segment broadcast | CRM | 4 minggu |
| 23 | **Customer Segment Dynamic** — Segmentasi otomatis berdasarkan behavior | CRM | 2 minggu |
| 24 | **FIFO Cost Method** — Hitung COGS otomatis dengan FIFO | Inventory | 3 minggu |
| 25 | **Average Cost** — Weighted average cost automasi | Inventory | 1 minggu |
| 26 | **Reorder Point** — Auto PO suggestion saat stok di bawah reorder | Inventory | 2 minggu |
| 27 | **Manajemen Aset** — Aset tetap, depresiasi, mutasi aset | Accounting | 3 minggu |
| 28 | **Return Pembelian** — Retur ke supplier dengan tracking | Purchase | 2 minggu |
| 29 | **Custom Report Builder** — Drag-drop filter, kolom, grouping | Reports | 4 minggu |
| 30 | **Sales Target & Forecasting** — Target per sales/branch, forecast | Analytics | 3 minggu |
| 31 | **Webhook** — Outbound webhook untuk integrasi eksternal | API | 2 minggu |
| 32 | **Employee Leave Management** — Cuti, sakit, izin, approval | HR | 2 minggu |
| 33 | **GPS Attendance** — Check-in dengan lokasi GPS | HR | 1 minggu |
| 34 | **Overtime Auto Calc** — Hitung lembur otomatis dari attendance | HR | 2 minggu |
| 35 | **PWA Full** — Register service worker, offline fallback page | Frontend | 1 minggu |

### Medium (6-12 Bulan)

| # | Fitur | Module | Estimasi |
|---|-------|--------|----------|
| 36 | **Deposit Customer / Prepaid** — Top-up saldo pelanggan | CRM | 2 minggu |
| 37 | **Supplier Scorecard** — Evaluasi supplier otomatis (ketepatan, kualitas) | Purchase | 2 minggu |
| 38 | **Dropship Mode** — PO langsung dari supplier ke customer | Sales | 3 minggu |
| 39 | **Multi Currency** — Transaksi multi mata uang | System | 3 minggu |
| 40 | **Offline Mode** — POS offline dengan sync saat online | POS | 6 minggu |
| 41 | **Mobile Push Notification** — FCM untuk mobile browser | Notification | 2 minggu |
| 42 | **SMS Gateway** — Notifikasi via SMS | Notification | 1 minggu |
| 43 | **WhatsApp API** — Notifikasi dan invoice via WA API (not link) | Notification | 3 minggu |
| 44 | **SSO / LDAP** — Single Sign-On, Active Directory | Security | 3 minggu |
| 45 | **OAuth2 Provider** — Jadi OAuth provider untuk app lain | API | 2 minggu |
| 46 | **SIEM Integration** — Export audit log ke SIEM eksternal | Security | 2 minggu |
| 47 | **Custom Dashboard Widget** — Drag-drop dashboard builder | Dashboard | 3 minggu |

### Low (12+ Bulan / V2)

| # | Fitur | Module | Estimasi |
|---|-------|--------|----------|
| 48 | **Production / BOM / Manufacturing** — Bill of Material, production order | Production | 8 minggu |
| 49 | **E-Wallet Internal** — Saldo internal untuk transaksi antar entitas | Finance | 4 minggu |
| 50 | **Document Management** — Upload, categorize, search dokumen | System | 4 minggu |

---

*Audit disusun oleh Senior Enterprise Software Architect / ERP Consultant / Technical Auditor*  
*APMS — Ashar Parfum Management System | July 2026*
/