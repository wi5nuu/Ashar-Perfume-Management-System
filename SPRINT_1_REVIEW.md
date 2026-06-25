# 🏁 Sprint 1 Review — APMS (Ashar Grosir Perfume Management System)

> **Sprint Duration:** April 12 – April 18, 2026 (Week 1)  
> **Sprint Review Date:** April 19, 2026  
> **Presented by:** Ashar (Solo Developer)  
> **Assisted by:** Antigravity AI Agent  

---

## 1. 🎯 Sprint Goal

> **"Membangun fondasi sistem manajemen toko parfum yang lengkap — mencakup autentikasi, manajemen produk, sistem POS, inventori, laporan keuangan, dan fitur pendukung operasional toko — sehingga aplikasi dapat digunakan secara penuh untuk operasional harian Ashar Grosir Parfum."**

*Translation: Build the complete foundation of the perfume store management system — including authentication, product management, POS system, inventory, financial reporting, and operational support features — so the application can be fully used for daily operations of Ashar Grosir Parfum.*

---

## 2. ✅ Progress Completed (Sprint 1)

### Core Features — Fully Implemented

| # | Feature | Details | Status |
|---|---------|---------|--------|
| 1 | **Authentication System** | Multi-role login (Owner, Admin, Cashier), login activity log, session management | ✅ Done |
| 2 | **Product Management** | CRUD produk, kategori berwarna, upload gambar, manajemen harga beli/jual | ✅ Done |
| 3 | **POS Transaction System** | Kasir real-time, multi-payment (cash/transfer/e-wallet), pajak, kupon, poin loyalitas, cetak struk | ✅ Done |
| 4 | **Inventory Management** | Stok real-time, audit stok, manajemen supplier, alert stok rendah | ✅ Done |
| 5 | **Financial Dashboard & Reports** | Laporan harian/mingguan/bulanan, grafik penjualan, KPI cards | ✅ Done |
| 6 | **Wholesale Module** | Order grosir, harga khusus, ongkos kirim, status pengiriman | ✅ Done |
| 7 | **Employee & Attendance** | Profil karyawan, shift buka/tutup, absensi, foto penutupan | ✅ Done |
| 8 | **Customer & Debt System** | Loyalitas poin, hutang pelanggan, riwayat pembelian | ✅ Done |
| 9 | **Expense & Coupon System** | Pengeluaran berkategori, kode kupon promosi | ✅ Done |
| 10 | **AI Assistant (Offline)** | Rekomendasi parfum, analitik AI, chat interface | ✅ Done |
| 11 | **System Settings** | Konfigurasi toko, logo, pajak, profil user | ✅ Done |

### Technical Achievements
- ✅ **47 database migrations** successfully run
- ✅ **20 Laravel controllers** implemented
- ✅ **19 view modules** created (Blade templates)
- ✅ **GitHub repository** active with regular commits
- ✅ **Mobile login page** — responsive centered layout fixed
- ✅ **Dashboard** — responsive grid layout optimized

### Partially Completed
| Feature | Progress |
|---------|---------|
| **Mobile Responsive UI (PB-11)** | 40% — Login ✅, Dashboard ✅, POS/Products/Reports pending |

---

## 3. 👤 Task Distribution

> *Note: This is a solo project. All tasks are handled by one developer (Ashar) with AI Agent assistance.*

| Task Category | Handled By | AI Support |
|--------------|-----------|-----------|
| Requirements & Business Logic | Ashar (Product Owner) | — |
| Sprint Planning & Documentation | Ashar (Scrum Master) | ✅ Documentation drafting |
| Backend Development (Laravel) | Ashar (Developer) | ✅ Code generation & review |
| Frontend / UI (Blade + Tailwind) | Ashar (Developer) | ✅ UI design & fixes |
| Database Design | Ashar (Developer) | ✅ Migration suggestions |
| Bug Fixing & QA | Ashar (Developer) | ✅ Bug diagnosis |
| Git Commits & Version Control | Ashar | — |

**Daily Contribution Breakdown (Sprint 1):**

| Day | Tasks Completed |
|-----|----------------|
| Day 1 (Apr 12) | Project setup, auth system, role middleware, migrations |
| Day 2 (Apr 13) | Product CRUD, categories, image upload |
| Day 3 (Apr 14) | POS system — cart, payment, receipt |
| Day 4 (Apr 15) | Inventory, stock audit, suppliers |
| Day 5 (Apr 16) | Financial reports, dashboard KPI, wholesale module |
| Day 6 (Apr 17) | Employee/shift/attendance, AI assistant integration |
| Day 7 (Apr 18) | Mobile responsive fixes (login, dashboard), QA review |

---

## 4. ⚠️ Challenges (Hambatan yang Dihadapi)

| # | Challenge | Impact |
|---|----------|--------|
| **C-01** | **Database Connection Error** — MySQL service tidak start secara otomatis di XAMPP | Menghambat testing lokal, development terhenti sementara |
| **C-02** | **Mobile Responsiveness** — Dashboard dan login form tidak tampil baik di layar kecil | UX buruk untuk pengguna kasir yang memakai tablet/HP |
| **C-03** | **AI Model Integration** — Integrasi offline AI model membutuhkan konfigurasi server tambahan | Setup kompleks, waktu lebih lama dari estimasi |
| **C-04** | **Role Permission Complexity** — 4 tipe role (Owner/Admin/Cashier/Wholesale) dengan akses berbeda | Middleware logic menjadi kompleks, ada edge case yang missed |
| **C-05** | **Solo Developer Bandwidth** — Semua task ditangani satu orang, sulit memisahkan fokus fe/be | Sprint velocity lebih rendah dari ideal tim 3 orang |

---

## 5. 💡 Solutions (Solusi yang Diterapkan)

| # | Challenge | Solution Applied |
|---|----------|----------------|
| **C-01** | Database Connection | Reconfigured `my.ini`, changed MySQL port to 3307, restarted XAMPP services manually — **Resolved ✅** |
| **C-02** | Mobile Responsive | Applied targeted CSS adjustments — flexbox centering for login, responsive grid breakpoints for dashboard — **Partially Resolved 🔄** |
| **C-03** | AI Integration | Used `OfflineAiController.php` with local model endpoint, separated online/offline modes — **Resolved ✅** |
| **C-04** | Role Permissions | Refactored middleware to use policy-based checks, added `owner` role to user roles migration — **Resolved ✅** |
| **C-05** | Solo Bandwidth | Leveraged AI Agent as pair programmer for boilerplate code, documentation, and debugging support — **Ongoing Strategy ✅** |

---

## 6. 📅 Plan for Week 2 (Sprint 2 Goals)

**Sprint 2 Duration:** April 19 – April 25, 2026

### Sprint 2 Goal
> *"Menyempurnakan tampilan mobile responsive di seluruh halaman aplikasi, melakukan pengujian menyeluruh (end-to-end testing), dan mempersiapkan aplikasi untuk demo presentasi final."*

### Sprint 2 Backlog Items

| # | Task | Priority | Estimated Points |
|---|------|----------|----------------|
| S2-01 | Complete Mobile Responsive — POS page | 🔴 High | 3 |
| S2-02 | Complete Mobile Responsive — Product list & catalog | 🔴 High | 2 |
| S2-03 | Complete Mobile Responsive — Reports tables | 🟡 Medium | 2 |
| S2-04 | Hamburger menu / collapsible sidebar for mobile | 🟡 Medium | 3 |
| S2-05 | End-to-end testing — all transaction flows | 🔴 High | 5 |
| S2-06 | Performance optimization — dashboard load time | 🟡 Medium | 3 |
| S2-07 | Final UI polish — typography, spacing, color consistency | 🟡 Medium | 3 |
| S2-08 | Prepare demo data & presentation documentation | 🟡 Medium | 2 |

**Total Sprint 2 Target:** 23 Story Points

### Week 2 Daily Plan

| Day | Plan |
|-----|------|
| Day 1 (Apr 19) | Sprint 1 Review session + Mobile POS responsive start |
| Day 2 (Apr 20) | Mobile product list + mobile reports |
| Day 3 (Apr 21) | Collapsible sidebar navigation |
| Day 4 (Apr 22) | End-to-end transaction testing |
| Day 5 (Apr 23) | Performance optimization |
| Day 6 (Apr 24) | UI polish & final adjustments |
| Day 7 (Apr 25) | Demo preparation + documentation update |

---

## 📊 Sprint 1 Summary Metrics

| Metric | Value |
|--------|-------|
| Sprint Duration | 7 days |
| Total Story Points Planned | 100 |
| Total Story Points Completed | 92 |
| Sprint Velocity | **92%** |
| Features Delivered | 11 / 12 |
| GitHub Commits | Active |
| Bugs Resolved | 5 |
| Bugs Pending | 0 critical |

---

*Prepared by: Ashar | Sprint 1 Review — April 19, 2026*  
*APMS — Ashar Grosir Perfume Management System*
