---
title: Accounting Module Reference
diataxis: reference
owner: Staff Software Engineer
update_frequency: on-change
classification: mandatory
---

# Accounting Module Reference (Double-Entry GL)

## Overview

Modul akuntansi APMS adalah **double-entry general ledger** lengkap:

- **Chart of Account (COA)** — kode akun standar 5 level, flag `is_posting`, `is_cash`, `is_bank`
- **Journal** — jurnal umum + reversal, nomor unik `JNL-YYYYMMDD-XXXXXX`
- **Periode akuntansi** — buka/tutup periode, validasi draft saat penutupan
- **Auto Posting** — jurnal otomatis dari transaksi operasional (idempoten & fail-safe)
- **Laporan** — Trial Balance, General Ledger, Income Statement, Balance Sheet, Cash Flow + export PDF

Prinsip inti:

1. **Jurnal hanya dibaca laporan jika berstatus `posted`** — draft tidak memengaruhi laporan.
2. **Auto-posting fail-safe** — jika posting gagal, transaksi operasional tetap jalan (skip + log), jangan pernah throw.
3. **Auto-posting idempoten** — setiap transaksi hanya boleh di-posting sekali (`transaction_type` + `transaction_id` unik, status != reversed).
4. Perubahan skema database bersifat **additive** (kolom baru, tidak menghapus kolom lama).

## Struktur File

```
app/Services/Accounting/
├── JournalService.php                # Buat jurnal, generate nomor, reverse, delete draft
├── AutoPostingService.php            # Posting otomatis dari semua transaksi operasional
├── FinancialStatementService.php     # Laporan: TB, IS, BS, CF
├── AccountingPeriodService.php       # Periode akuntansi
└── AccountingAlreadyPostedException.php  # Throw saat posting duplikat

app/Models/
├── ChartOfAccount.php                # COA + balanceBetween()
├── JournalEntry.php                  # Header jurnal
├── JournalDetail.php                 # Detail debit/kredit
└── AccountingPeriod.php              # Periode

config/accounting.php                 # Kode akun default + toggle enabled
routes/accounting.php                 # Web routes modul akuntansi
database/seeders/ChartOfAccountSeeder.php
database/migrations/2026_08_15_000001..000003_*.php
resources/views/accounting/
├── index.blade.php                   # Dashboard akuntansi
├── coa/{index,create,edit}.blade.php
├── journal/{index,show,create}.blade.php
├── ledger/index.blade.php
├── periods/index.blade.php
└── exports/*-pdf.blade.php           # 5 template PDF (DomPDF)
tests/Feature/AccountingEnterpriseTest.php
```

## Skema Data

| Tabel | Kolom penting |
|---|---|
| `chart_of_accounts` | `code`, `name`, `type` (asset/liability/equity/revenue/expense), `normal_balance`, `level`, `parent_id`, `is_active`, `is_posting`, `is_cash`, `is_bank`, `description` |
| `journal_entries` | `journal_number`, `journal_date`→`date`, `description`, `status` (draft/posted/reversed), `approved_by`, `approved_at`, `reversed_by`, `reversed_at`, `reversal_of_id`, `transaction_type`, `transaction_id`, `branch_id` |
| `journal_details` | `journal_entry_id`, `account_id`, `contact_type`, `contact_id` (customer/supplier), `debit`, `credit`, `memo` |
| `accounting_periods` | `name`, `start_date`, `end_date`, `is_closed`, `closed_at`, `closed_by` |

Uniqueness: `transaction_type + transaction_id` unik saat `status != reversed` (idempotensi auto-posting).

## Alur Kerja

### 1. Jurnal Manual (`JournalService`)

```
create(date, description, entries[{account_id, debit|credit, memo, contact_type?, contact_id?}], branch?)
  → validasi: tidak kosong, balance (debit == credit), hanya akun is_posting
  → status = draft, nomor JNL-YYYYMMDD-XXXXXX (urutan harian)
  → buat detail; hanya satu sisi (debit ATAU credit) per baris

approve(journal)   → draft → posted (set approved_by, approved_at)
reverse(journal)   → posted → saldo dibalik, status reversed, linked via reversal_of_id
delete(journal)    → hanya draft yang bisa dihapus
```

### 2. Auto Posting (`AutoPostingService`)

Dipanggil dari controller operasional (transaksi operasional **bukan** bloker):

| Sumber | Akun debit | Akun kredit | Catatan |
|---|---|---|---|
| Penjualan retail (`Transaction`) | `receivable_cash` / piutang | `revenue_retail` | HPP: `expense_cogs` ↔ `inventory_merchandise` via `transaction_details.purchase_price` |
| Penjualan grosir (`WholesaleOrder`) | `receivable_cash` / piutang | `revenue_wholesale` | HPP dihitung dari `product.purchase_price` |
| Retur penjualan (`SalesReturn`) | `revenue_*` | `receivable_cash` / piutang | HPP dibalik via `transactionDetail.purchase_price` |
| Pembelian (`PurchaseOrder`) | `inventory_merchandise` | `payable_account` / kas | |
| Beban (`Expense`) | `expenseAccountFor(category)` | kas | Pemetaan keyword → akun COA |
| Gaji (`Payroll`) | `expense_salary` | kas | Per batch, key `crc32(batch)` |

Setiap handler: cek `enabled()` → `existsForSource()` → `journalService->create()` → `approve()`.
Jika gagal di tengah: log warning (`accounting_skip`), alur operasional tetap dilanjutkan.

### 3. Laporan (`FinancialStatementService`)

Semua query **hanya** membaca jurnal `posted` (status != draft, tidak reversed):

| Laporan | Basis |
|---|---|
| Trial Balance | Saldo per akun posting, debit/credit, selisih = 0 |
| General Ledger | Detail jurnal + running balance, opening/closing balance |
| Income Statement | Revenue − COGS − operating expense (`expense_accounts` mengecualikan COGS) |
| Balance Sheet | Aset = Liabilitas + Ekuitas; `balanceBetween(null, end)` → saldo sampai tanggal |
| Cash Flow | Metode tidak langsung: perubahan kas dari aktivitas operasi/investasi/pendanaan |

## Konfigurasi (`config/accounting.php`)

```php
'enabled' => true,                       // master toggle auto-posting
'journal' => [ 'number_prefix' => 'JNL' ],
'accounts' => [ 'cash' => '1-111', 'receivable_cash' => '1-211', ... ],
```

## Perilaku Khusus & Guard Rails

- `ChartOfAccount::balanceBetween(?string $startDate, string $endDate)` — `$startDate = null` berarti "sejak awal"; dipakai Balance Sheet.
- `postPayroll()` & `postPayrollBatch()` — hanya posting jika payroll `enabled()` dan belum pernah di-posting; jangan pernah throw saat duplicate.
- Delete jurnal draft: hapus detail + header; jurnal posted harus di-reverse, bukan dihapus.
- Penutupan periode: menolak jika masih ada jurnal draft dalam rentang tanggal periode.

## Test

`tests/Feature/AccountingSmokeTest.php` (51 test, 186 assertions) — suite utama end-to-end:

- Dashboard akuntansi accessible
- COA: create akun baru (asset, liability, equity, revenue, expense)
- Jurnal manual: seimbang / tidak seimbang (harus reject) / akun non-posting (harus reject)
- Nomor jurnal unik & berurutan
- Auto-posting dari 5 sumber transaksi operasional: POS retail, wholesale (store + confirm), sales return (store + approve), goods receipt (store), debt payment, payroll batch generate
- Setiap auto-posting diverifikasi: jurnal `posted` muncul di GL
- Auto-posting idempoten: 2x panggil → tetap 1 jurnal
- 4 laporan keuangan: Trial Balance, Income Statement, Balance Sheet, Cash Flow
- General Ledger per akun
- Periode akuntansi: buka & tutup
- 5 export PDF: TB, IS, BS, CF, GL

Jalankan: `php artisan test --filter=AccountingSmokeTest`
