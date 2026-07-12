---
title: Aturan & Alur Kerja — Software Engineer
pegawai: Siti Nurfadhilah Az Zahra Syam
periode: 15 Juli 2026 — 15 Desember 2026
tools: Jira, GitHub, Slack
update_frequency: mingguan
---

# Aturan & Alur Kerja Software Engineer

## 1. Alur Kerja Harian

1. **Cek Jira** setiap pagi — lihat task yang diassign oleh lead
2. **Kerjakan task** sesuai prioritas (P1 → P2 → P3)
3. **Update status Jira**:
   - `To Do` → `In Progress` saat mulai
   - `In Progress` → `In Review` saat siap direview
   - `In Review` → `Done` setelah disetujui lead
4. **Comment di Jira** saat task selesai — tulis ringkasan apa yang dikerjakan
5. **Commit & Push** kode ke GitHub dengan referensi Jira (`APMS-123`)

---

## 2. Aturan Task

### Yang Wajib Dikerjakan

- Semua task yang diassign di Jira oleh lead
- Ikuti prioritas: **P1 (High) → P2 (Medium) → P3 (Low)**
- Jika task mentok > 2 jam, **tanya lead** — jangan diam
- Setiap task selesai → **test dulu** sebelum minta review

### Dokumentasi Wajib

- Setiap fitur baru → update `docs/` sesuai Diátaxis
- Setiap perubahan signifikan → buat commit message jelas
- Setiap akhir minggu → **isi laporan mingguan** di file `.md`

### Larangan

- Tidak boleh push langsung ke `master` — wajib **Pull Request**
- Tidak boleh menyimpan credential di kode
- Tidak boleh mengerjakan task di luar Jira tanpa izin lead

---

## 3. Progress Sistem Saat Ini

### Kondisi Sebelum (Juni 2026)

| Aspek | Sebelum |
|---|---|
| **Manajemen Task** | Tidak ada — komunikasi via chat |
| **Dokumentasi** | Tidak ada dokumentasi teknis |
| **Wholesale Customer** | Tidak ada fitur lupa password, tidak ada request reset |
| **Keamanan** | Password hardcoded di seeder, tidak ada audit log |
| **Testing** | Hanya beberapa test dasar |
| **Queue** | Tidak terkelola |
| **Error Handling** | Minim — banyak 500 error tidak tertangani |

### Kondisi Setelah (Juli 2026)

| Aspek | Sesudah |
|---|---|
| **Manajemen Task** | **Jira** untuk daily task & tracking |
| **Dokumentasi** | **99 file docs** (Diátaxis: Tutorials, How-To, Reference, Explanation) |
| **Wholesale Customer** | ✅ Lupa password, ✅ Request reset ke owner, ✅ Lihat pesanan |
| **Keamanan** | ✅ Password random di seeder, ✅ Audit log, ✅ Rate limiting, ✅ Input sanitizer, ✅ Session security |
| **Testing** | ✅ PHPUnit, ✅ PHPStan Level 5 |
| **Queue** | ✅ Database queue driver |
| **Error Handling** | ✅ Custom error pages (403, 404, 429, 500, 503), ✅ try-catch di dashboard |
| **Roles** | ✅ 6 roles (owner, admin_pusat, admin_cabang, warehouse, employee, wholesale_customer) |
| **RBAC** | ✅ Role & permission management via admin panel |
| **AI** | ✅ Claude AI copilot, ✅ Rule-based fallback, ✅ Business insights dashboard |
| **Middleware** | ✅ 8 custom middleware (security, throttle, sanitizer, dll) |

---

## 4. Kekurangan / Pekerjaan Rumah (Backlog)

| # | Kekurangan / PR | Prioritas | Jira Epic |
|---|---|---|---|
| 1 | **Notifikasi WhatsApp** — Order status belum terkirim ke customer | P1 | EPIC-WHATSAPP |
| 2 | **RajaOngkir** — Ongkos kirim & tracking masih manual | P1 | EPIC-SHIPPING |
| 3 | **Report Advanced** — Custom range, pivot, Excel export masih terbatas | P1 | EPIC-REPORTS |
| 4 | **Multi Language** — Hanya bahasa Indonesia, belum ada EN | P2 | EPIC-LANG |
| 5 | **Portal Customer v2** — UI masih dasar, kurang mobile-friendly | P2 | EPIC-PORTAL-v2 |
| 6 | **Barcode Scanner** — Stok in/out masih manual input | P2 | EPIC-BARCODE |
| 7 | **2FA** — Belum ada two-factor authentication | P2 | EPIC-2FA |
| 8 | **PPOB** — Tagihan PDAM, PLN, pulsa belum terintegrasi | P3 | EPIC-PPOB |
| 9 | **Redis + Horizon** — Queue & cache masih pakai database/file | P3 | EPIC-INFRA |
| 10 | **Offline POS** — Tidak bisa transaksi saat internet mati | P3 | EPIC-OFFLINE-POS |

---

## 5. Tantangan Teknis

| Tantangan | Deskripsi |
|---|---|
| **Multi-Branch Data Isolation** | Setiap cabang punya data sendiri (inventory, transaksi, expense) — harus dipastikan tidak bocor antar cabang |
| **Race Condition** | Stok bisa oversell jika 2 transaksi terjadi bersamaan —但 sudah pakai `lockForUpdate()` |
| **Soft Delete Complexity** | Banyak model pakai SoftDeletes — query harus hati-hati dengan `withTrashed()` |
| **Performance** | Report dengan ribuan transaksi butuh query optimization & indexing |
| **Security** | Menangani data sensitif (NIK, NPWP, bank) — harus terenkripsi |
| **Real-time** | Nanti butuh websocket (Reverb) untuk notifikasi real-time |

---

## 6. Laporan

### Laporan Harian (di Jira)

Setiap hari, di comment Jira task yang selesai:

```
### Daily Report — {Tanggal}

Selesai:
- APMS-123: {ringkasan}
- APMS-124: {ringkasan}

Kendala:
- {kendala jika ada}

Plan:
- {rencana selanjutnya}
```

### Laporan Mingguan (di file `.md` + Jira)

Setiap akhir minggu:
1. Buka template di `aturan-task/templates/laporan-mingguan.md`
2. Copy ke folder bulan berjalan: `agustus/minggu-1.md`
3. Isi sesuai template
4. Post juga ke Jira sebagai **daily report** di task khusus

---

## 7. Struktur Folder

```
aturan-task/
├── README.md                         # File ini — aturan main
├── templates/
│   ├── laporan-mingguan.md           # Template laporan mingguan
│   └── laporan-harian.md             # Template laporan harian
├── agustus/
│   ├── minggu-1.md
│   ├── minggu-2.md
│   ├── minggu-3.md
│   └── minggu-4.md
├── september/
│   ├── minggu-1.md
│   └── ...
├── oktober/
├── november/
└── desember/
```
