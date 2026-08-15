# Documentation — APMS (Ashar Parfum Management System)

**Last Updated:** August 15, 2026
**Version:** Laravel 12, PHP 8.2+, MySQL 8.0
**Developer:** Wisnu Alfian Nur Ashar

---

## Quick Navigation

### System Overview
- [ERP System Audit](ERP_SYSTEM_AUDIT.md) — modul lengkap, skor kualitas, roadmap
- [Architecture](APMS-ENTERPRISE-ARCHITECTURE.md) — arsitektur sistem enterprise
- [Transaction Flow](TRANSACTION_FLOW.md) — alur transaksi POS, grosir, retur, payroll
- [Changelog](CHANGELOG_UPGRADE.md) — riwayat perubahan & patch

### Module Reference
- [Accounting Module](reference/accounting/accounting-module.md) — double-entry GL, auto-posting, laporan keuangan
- [Racikan Parfum](RACIKAN.md) — standar racikan bibit, rasio porsi ml, implementasi kode

### Development
- [Bug Fix Log](BUGFIX_LOG.md) — semua bug yang telah diperbaiki (BUG-01 s/d BUG-37)

---

## Modul Sistem

| Modul | Status |
|-------|--------|
| Point of Sale (POS) | ✅ Production-ready |
| Wholesale / B2B Grosir | ✅ Production-ready |
| Inventory Management | ✅ Production-ready |
| Product Management | ✅ Production-ready |
| Customer Management | ✅ Production-ready |
| Employee & HR | ✅ Production-ready |
| Attendance | ✅ Production-ready |
| Shift Management | ✅ Production-ready |
| Payroll | ✅ Production-ready |
| Commission | ✅ Production-ready |
| **Accounting (Double-Entry GL)** | ✅ Production-ready |
| Reports & Analytics | ✅ Production-ready |
| Export (PDF/CSV/Excel) | ✅ Production-ready |
| Coupon / Promo | ✅ Production-ready |
| Security & RBAC | ✅ Production-ready |
| AI Copilot | ✅ Production-ready |

---

## Tech Stack

| Layer | Detail |
|-------|--------|
| Framework | Laravel 12.x |
| Language | PHP 8.2+ |
| Frontend | Blade + Bootstrap 5 + Alpine.js + Tailwind CSS |
| Database | MySQL 8.0+ (production), SQLite (testing) |
| Auth | Session + Laravel Sanctum + 2FA (TOTP) |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| Testing | PHPUnit — **51 tests, 186 assertions** |