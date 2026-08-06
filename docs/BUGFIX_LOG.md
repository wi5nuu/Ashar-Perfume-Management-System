# Bug Fix Log — APMS

Catatan semua bug yang telah diperbaiki beserta lokasi kode dan penjelasan teknis.

---

## Sesi 1 — Perbaikan Modul Utama

### BUG-01: WholesaleController — Stok tidak dipotong saat order pending
- **File:** `app/Http/Controllers/WholesaleController.php`
- **Masalah:** Stok tidak dikurangi saat order grosir dibuat dengan status pending.
- **Fix:** Potong stok di `store()`, kembalikan di `cancel()`/`destroy()`/`update()`,
  hapus pemotongan ganda di `complete()`. Tambah method `restoreBulkStock()`.

### BUG-02: SalesReturnController — Error 500 di index
- **File:** `app/Http/Controllers/SalesReturnController.php`
- **Masalah:** Memanggil `route('returns.create')` tanpa parameter wajib `{transaction}`.
- **Fix:** Hapus pemanggilan route tersebut dari index, fix KPI variables,
  fix kolom `return_detail_id` → `transaction_detail_id`.

### BUG-03: InventoryController — `$inventories->count()` error
- **File:** `resources/views/inventory/index.blade.php`
- **Masalah:** `count()` dipanggil pada LengthAwarePaginator, bukan collection.
- **Fix:** Ganti dengan `->total()` untuk paginator.

### BUG-04: ExpenseController — `totalExpenses` dihitung setelah paginate
- **File:** `app/Http/Controllers/ExpenseController.php`
- **Masalah:** Query sum dipanggil setelah `paginate()` sehingga hanya menghitung
  halaman aktif, bukan semua data.
- **Fix:** Gunakan `(clone $query)->sum('amount')` sebelum `paginate()`.

### BUG-05: ShiftController — `branch_id` tidak disimpan
- **File:** `app/Http/Controllers/ShiftController.php`
- **Masalah:** `Shift::create()` tidak menyertakan `branch_id`.
- **Fix:** Tambah `branch_id` saat create, fix scope `$discrepancy`.

### BUG-06: CommissionController — `now()->parse()` tidak valid
- **File:** `app/Http/Controllers/CommissionController.php`
- **Masalah:** `now()->parse()` bukan method Carbon yang valid.
- **Fix:** Ganti dengan `Carbon::parse()`, tambah `use Carbon\Carbon`.

---

## Sesi 2 — Transaksi Retail & Stok Bibit

### BUG-07: StoreTransactionRequest — Validasi stok pakai kolom yang salah
- **File:** `app/Http/Requests/StoreTransactionRequest.php:95`
- **Masalah:** Validasi menggunakan `bulk_stock_ml` tapi controller menggunakan
  `current_stock`. Semua transaksi produk reguler gagal validasi karena
  `bulk_stock_ml` umumnya 0.
- **Fix:** Ganti `$inventory->bulk_stock_ml` → `$inventory->current_stock`.

### BUG-08: Form Pelanggan Baru POS — Terlalu kompleks untuk retail
- **File:** `resources/views/transactions/create.blade.php`
- **Masalah:** Form meminta NIK, gender, tanggal lahir, email, alamat (8 field)
  untuk pelanggan walk-in retail.
- **Fix:** Sederhanakan menjadi nama + nomor HP saja, tipe otomatis `retail`.

### BUG-09: Form Pembayaran POS — Metode tidak lengkap
- **File:** `resources/views/transactions/create.blade.php`
- **Masalah:** Hanya ada Cash, QRIS, Transfer. Backend mendukung ewallet,
  debit_card, credit_card tapi tidak ada di UI.
- **Fix:** Tambah E-Wallet (DANA/OVO/GoPay/ShopeePay), Debit Card, Credit Card.
  Sub-field dinamis muncul saat pilih E-Wallet atau Transfer.
  `ewallet_type` dan `transfer_type` dikirim ke server.

### BUG-10: Validasi bonus premium — Tidak ada enforced
- **File:** `resources/views/transactions/create.blade.php`
- **Masalah:** Kasir bisa proses transaksi dengan item Premium tanpa memilih
  bonus aroma gratis.
- **Fix:** Tambah validasi di `processPayment()` — blokir transaksi jika ada
  item Premium tanpa bonus. Scroll otomatis ke section Parfum Bonus.
  Warning real-time `#premiumBonusWarning` muncul di atas tombol Bayar.

### BUG-11: Kalkulasi stok bibit — Tidak mempertimbangkan tier/kualitas
- **File:** `app/Http/Controllers/TransactionController.php:203`,
  `app/Http/Requests/StoreTransactionRequest.php:76`
- **Masalah:** Semua kualitas (Premium/Sedang/Biasa) menggunakan ukuran botol
  penuh sebagai ml yang dikurangi dari stok (30ml → 30ml).
- **Fix:** Implementasi tabel standar racikan per tier:
  - Premium: 30ml=20ml, 50ml=33ml, 100ml=65ml
  - Sedang:  30ml=15ml, 50ml=25ml, 100ml=50ml
  - Biasa:   30ml=10ml, 50ml=17ml, 100ml=33ml
- **Detail:** Lihat `docs/RACIKAN.md` untuk penjelasan lengkap.

---

## Sesi 3 — Bug Fix All Pages

### BUG-12: accounting.php — Role middleware salah
- **File:** `routes/accounting.php:6`
- **Masalah:** `role:kasir` tidak cocok dengan nilai role di database (`cashier`).
  Kasir tidak bisa mengakses modul accounting.
- **Fix:** Ganti `role:owner,admin,kasir` → `role:owner,admin,cashier`.

### BUG-13: ExpenseController — Variabel KPI tidak dikirim ke view
- **File:** `app/Http/Controllers/ExpenseController.php:34`
- **Masalah:** View menggunakan `$lastMonthExpenses`, `$dailyAverage`, `$topCategory`
  tapi controller hanya mengirim `$expenses` dan `$totalExpenses`.
- **Fix:** Tambah kalkulasi dan pengiriman 3 variabel KPI ke view.

### BUG-14: StockRequestController — Stats tidak di-scope per branch
- **File:** `app/Http/Controllers/StockRequestController.php:41`
- **Masalah:** Stats `pending/shipped/received` dihitung secara global
  (semua branch), tidak konsisten dengan query utama yang sudah di-scope
  per branch/role.
- **Fix:** Terapkan scope branch yang sama pada query stats.

### BUG-15: SalesTargetController — Route resource penuh tanpa method lengkap
- **File:** `routes/web.php:333`
- **Masalah:** `Route::resource()` mendaftarkan 7 route tapi controller hanya
  punya 4 method (`index`, `create`, `store`, `show`). Route `edit`, `update`,
  `destroy` akan menghasilkan error 500.
- **Fix:** Batasi dengan `->only(['index', 'create', 'store', 'show'])`.

---

## Format Penambahan Bug Baru

```
### BUG-XX: NamaController/File — Deskripsi singkat
- **File:** path/ke/file.php:baris
- **Masalah:** Penjelasan bug
- **Fix:** Penjelasan solusi
```
