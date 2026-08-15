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

## Sesi 4 — Modul Akuntansi Enterprise

### BUG-16: Migrasi — SQLite gagal total (semua test error)
- **File:** `database/migrations/2026_06_27_000001_fix_remaining_schema_issues.php`
- **Masalah:** `safeCreateIndex()` membuat index `idx_coupon_status` pada kolom
  `coupons.status` yang tidak ada (MySQL menolak diam-diam, SQLite mengizinkan).
  Akibatnya migrasi `2026_08_06_140121` (DROP COLUMN `new_password` pada
  `password_reset_requests`) gagal di SQLite: `error in index idx_coupon_status
  after drop column: no such column: status`. Semua test suite error di migrasi.
- **Fix:** `safeCreateIndex()` kini cek `Schema::hasColumn()` sebelum membuat
  index. Index baru `idx_coupon_status` tidak dibuat sama sekali (kolom memang
  tidak ada).

### BUG-17: AutoPostingService — postPayroll throw saat payroll dinonaktifkan
- **File:** `app/Services/Accounting/AutoPostingService.php`
- **Masalah:** `postPayroll()` langsung memanggil `guard()` tanpa cek `enabled()`.
  Payroll yang dinonaktifkan akan melempar exception tak tertangkap.
- **Fix:** Tambah cek `enabled()` + `existsForSource('payroll', ...)` sebelum
  commit; gagal → log `accounting_skip` + return, bukan throw.

### BUG-18: AutoPostingService — COGS grosir/retur salah kolom
- **File:** `app/Services/Accounting/AutoPostingService.php`
- **Masalah:** `wholesale_order_details` dan `sales_return_items` tidak memiliki
  kolom `purchase_price` (hanya `transaction_details` yang punya). Query COGS
  menghasilkan error SQL.
- **Fix:** `postWholesaleOrder()` menghitung COGS via
  `details()->with('product')` → `product.purchase_price * quantity`;
  `postSalesReturn()` via `items()->with('transactionDetail')` →
  `transactionDetail.purchase_price * quantity`.

### BUG-19: FinancialStatementService — COGS dihitung ganda di Income Statement
- **File:** `app/Services/Accounting/FinancialStatementService.php`
- **Masalah:** Akun COGS (`expense_cogs`) muncul di daftar expenses sekaligus
  di baris COGS terpisah → total beban double-count.
- **Fix:** Daftar `expenses` kini mengecualikan akun COGS; `total_expense` =
  beban operasional saja; `net_income` tetap = revenue − seluruh beban
  (termasuk COGS). Tambah key `operating_expense`.

### BUG-20: AutoPostingService — expenseAccountFor fallback salah
- **File:** `app/Services/Accounting/AutoPostingService.php`
- **Masalah:** Saat kategori expense tidak dikenal, fallback terakhir mengarah
  ke `revenue_other` (salah akun untuk beban).
- **Fix:** Tambah peta keyword (employee salary/gaji → expense_salary, sewa,
  listrik, transport, marketing, administrasi, dll) + pencocokan nama akun COA +
  fallback akhir `expense_other` (`5-113`) yang ditambahkan ke
  `config/accounting.php`.

### BUG-21: IpBlacklist — block() error "no such column: attempts" (pra-eksisting)
- **File:** `app/Models/IpBlacklist.php`
- **Masalah:** `updateOrCreate()` memakai `DB::raw('attempts + 1')` — saat
  record IP baru di-INSERT, SQL merujuk kolom `attempts` yang belum ada di baris
  yang sama → `SQLSTATE no such column: attempts`. Test `EnterpriseSecurityTest
  > ip blacklist blocking` gagal.
- **Fix:** Terapkan pola `recordAttempt()`: `updateOrCreate(..., ['attempts' => 1])`
  lalu `increment('attempts')` hanya jika `!wasRecentlyCreated`.

### BUG-22: Registrasi user — password diam-diam terbuang (NOT NULL constraint)
- **File:** `app/Http/Controllers/Auth/RegisteredUserController.php`
- **Masalah:** `User::create()` menyertakan `password`, padahal `password`
  sengaja TIDAK ada di `$fillable` model User (anti mass-assignment). Password
  dibuang diam-diam → INSERT gagal `NOT NULL constraint failed: users.password`.
  Registrasi rusak total di produksi; test `RegistrationTest` & `AuthSecurityTest
  > register` gagal.
- **Fix:** Buat `new User()` lalu set `password` secara eksplisit (`->save()`).
  Cast `'hashed'` di model meng-hash otomatis.

### BUG-23: Login throttle — 429 tanpa pesan error
- **File:** `routes/auth.php:27`
- **Masalah:** Route POST login punya middleware `throttle:5,1` (per-IP) DAN
  `LoginRequest::ensureIsNotRateLimited()` (per email|IP, limit 5). Di percobaan
  ke-6, middleware route mengembalikan 429 tanpa session errors sehingga
  `assertSessionHasErrors('email')` gagal.
- **Fix:** Naikkan throttle route ke `throttle:10,1` — rate limiter aplikasi
  (dengan pesan error ramah + event Lockout) aktif lebih dulu; proteksi per-IP
  tetap ada untuk serangan volume tinggi.

### BUG-24: Ganti password (Auth & Settings) — password tidak pernah berubah
- **File:** `app/Http/Controllers/Auth/PasswordController.php`,
  `app/Http/Controllers/SettingController.php:197`
- **Masalah:** `$user->update(['password' => Hash::make(...)])` — `password`
  tidak di `$fillable` → update diam-diam no-op. Password lama tetap aktif
  (masalah keamanan nyata); test `PasswordUpdateTest` & `ProfileTest` gagal.
- **Fix:** `forceFill(['password' => ...])->save()` (cast `'hashed'` otomatis
  hash).

### BUG-25: Auto-reset password — meng-update kolom yang sudah di-drop
- **File:** `app/Http/Controllers/Auth/CustomForgotPasswordController.php:68`
- **Masalah:** Auto-reset meng-update `new_password` di
  `password_reset_requests` — kolom itu sudah di-drop migrasi
  `2026_08_06_140121` (security fix, plaintext storage). Di produksi akan throw
  `SQLSTATE[42S22] unknown column`; update `password` user juga no-op karena
  bukan fillable.
- **Fix:** Hapus update `new_password`; set password user via `forceFill`.

### BUG-26: Admin ganti password & approve reset — password no-op
- **File:** `app/Http/Controllers/Admin/SecurityController.php:170`,
  `app/Http/Controllers/SettingController.php:425`
- **Masalah:** `$user->update(['password' => Hash::make(...)])` — password tidak
  di `$fillable` → password tidak pernah berubah.
- **Fix:** `forceFill(['password' => ...])->save()`.

### BUG-27: Karyawan baru — password dibuang saat User::create($validated)
- **File:** `app/Http/Controllers/EmployeeController.php:105`
- **Masalah:** `$validated['password']` di-set lalu `User::create($validated)` —
  password bukan fillable → dibuang diam-diam. Karyawan akses login tidak akan
  pernah bisa login.
- **Fix:** Keluarkan `password` dari array, `create()` sisanya, lalu
  `forceFill(['password' => ...])->save()`.

### BUG-28: Seeder akun — password dibuang diam-diam
- **File:** `app/Console/Commands/SetupRealPaketUsaha.php`,
  `app/Console/Commands/SeedWholesaleLeads.php`
- **Masalah:** `User::create()` menyertakan `password` (bukan fillable) →
  akun dibuat tanpa password (login mustahil).
- **Fix:** Set `password` eksplisit setelah create.

---

## Sesi 5 — Verifikasi End-to-End (Smoke Test)

Smoke test `tests/Feature/AccountingSmokeTest.php` (29 assertions) ditemukan
oleh review: suite lama hanya menguji service secara unit, TIDAK me-render
halaman. Bug di bawah ini lolos dari suite lama namun nyata di produksi.

### BUG-29: Dashboard akuntansi — ParseError 500 (halaman rusak total)
- **File:** `resources/views/accounting/index.blade.php:281-292`
- **Masalah:** `@json($chartData['labels'] ?? ['Jan',...,'Jun'])` — kompiler
  Blade `compileJson` memakai `explode(',')` naif. Array literal 6 item
  terpotong → `json_encode($x ?? ['Jan', 'Feb', 'Mar')` → ParseError
  "Unclosed '[' does not match ')'" saat render. `view:cache` TIDAK
  mendeteksi karena hanya compile, tidak execute.
- **Fix:** Ganti `@json` → `@js` (meng-handle ekspresi penuh). Array 3 item
  kebetulan lolos (kurung tutup ikut terambil) — array ≥4 item pasti rusak.

### BUG-30: Route accounting tanpa middleware group 'web'
- **File:** `routes/accounting.php`
- **Masalah:** File dimuat via callback `then()` di `bootstrap/app.php` yang
  TIDAK mendapat default middleware group. Route accounting hanya punya
  `['auth', 'role:...']` → tanpa session, CSRF (form POST → 419), dan
  `$errors` undefined di view (`coa/edit.blade.php`).
- **Fix:** Bungkus seluruh route dengan `Route::middleware('web')->group(...)`
  (pola yang sama dengan `routes/auth.php`).

### BUG-31: coaUpdate — Undefined array key "parent_id"
- **File:** `app/Http/Controllers/AccountingController.php:90`
- **Masalah:** `$validated['parent_id']` diakses langsung padahal field
  nullable dan bisa tidak dikirim.
- **Fix:** Gunakan `!empty($validated['parent_id'])`.

### BUG-32: Validasi jurnal — debit/credit null ditolak
- **File:** `app/Http/Controllers/AccountingController.php:178-179`
- **Masalah:** Aturan `required_without` + `numeric` menolak `null` — baris
  yang hanya debit atau hanya credit (form mengirim satu sisi null) gagal
  validasi.
- **Fix:** Ubah ke `nullable|numeric|min:0` (JournalService tetap memvalidasi
  keseimbangan).

---

## Format Penambahan Bug Baru

```
### BUG-XX: NamaController/File — Deskripsi singkat
- **File:** path/ke/file.php:baris
- **Masalah:** Penjelasan bug
- **Fix:** Penjelasan solusi
```

## Sesi 3 — Verifikasi End-to-End (Smoke Test HTTP)

### BUG-33: GoodsReceiptController::store — Auto-posting goods receipt tidak terhubung
- **File:** `app/Http/Controllers/GoodsReceiptController.php:100`
- **Masalah:** Penerimaan barang (GoodsReceipt) tidak pernah memposting jurnal
  double-entry — `AutoPostingService::postGoodsReceipt()` ada tapi tidak
  dipanggil dari mana pun. Pembelian tidak tercatat di GL.
- **Fix:** Panggil `postGoodsReceipt($receipt)` di dalam transaction store
  (fail-safe, idempotent: skip jika sudah ada jurnal untuk sumber yang sama).

### BUG-34: WholesaleController::store — NOT NULL constraint package_target_amount
- **File:** `app/Http/Controllers/WholesaleController.php:180`
- **Masalah:** Kolom `wholesale_orders.package_target_amount` NOT NULL tanpa
  default, tapi validasi `nullable` — order grosir tanpa target amount gagal
  insert (error 500/Integrity constraint) di MySQL production maupun SQLite.
- **Fix:** Default `?? 0` saat create dan update.

### BUG-35: goods_receipts.branch_id NOT NULL bertentangan dengan desain "Stok Pusat"
- **File:** `database/migrations/2026_06_24_000001_create_goods_receipts_table.php:24`
- **Masalah:** View menyediakan opsi "Stok Pusat (semua cabang)" (branch_id
  kosong) dan controller mengirim null, tapi kolom dibuat NOT NULL — owner
  yang memilih stok pusat selalu gagal insert (Integrity constraint) di MySQL
  strict mode.
- **Fix:** Migrasi baru `2026_08_15_000004_make_goods_receipts_branch_id_nullable_table.php`
  mengubah kolom menjadi nullable (migrasi lama tidak diubah).

### BUG-36: GoodsReceiptController::store — type 'receipt' tidak ada di enum inventory_movements
- **File:** `app/Http/Controllers/GoodsReceiptController.php:173`
- **Masalah:** `InventoryMovement::record(type: 'receipt')` padahal enum kolom
  `inventory_movements.type` hanya berisi sale/bonus/return/purchase/
  adjustment/transfer_in/transfer_out/void — insert selalu gagal (CHECK
  constraint di SQLite, strict mode di MySQL).
- **Fix:** Ganti ke `'purchase'` (penerimaan barang dari supplier).

### BUG-37: DebtController — GREATEST() tidak kompatibel SQLite
- **File:** `app/Http/Controllers/DebtController.php:94`
- **Masalah:** `GREATEST(0, debt_amount - ?)` valid di MySQL tapi error
  "no such function" di SQLite — alur pembayaran piutang tidak bisa diuji
  di environment test SQLite.
- **Fix:** Ganti dengan `CASE WHEN (debt_amount - ?) < 0 THEN 0 ELSE debt_amount - ? END`
  (setara, tetap atomik, portabel kedua DB).
