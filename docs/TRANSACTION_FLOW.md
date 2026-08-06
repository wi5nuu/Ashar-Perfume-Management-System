# Alur Transaksi — APMS

Dokumen ini menjelaskan alur lengkap transaksi retail dan grosir,
dari input kasir hingga pemotongan stok dan penyimpanan data.

---

## Transaksi Retail (POS)

### URL
`GET /transactions/create` → `TransactionController@create`

### Alur Frontend

```
Kasir buka halaman POS
  │
  ├─ Pilih Tipe Pelanggan (#customerType)
  │    ├─ retail  → harga normal
  │    └─ wholesale → harga × 0.9
  │
  ├─ Pilih Pelanggan (opsional)
  │    ├─ Pilih dari dropdown (#customerSelect) → customer_id terisi
  │    └─ Pelanggan Baru → form nama + HP → POST /api/customers
  │
  ├─ Tambah Produk ke Cart
  │    ├─ Klik produk → tierModal muncul
  │    ├─ Pilih ukuran (30ml/50ml/100ml) & kualitas (premium/sedang/biasa)
  │    └─ addToCartWithTier(tier) → item masuk cart dengan tier tersimpan
  │
  ├─ [Jika ada item Premium] → WAJIB pilih bonus aroma di #bonusParfumSelect
  │    └─ addParfumBonus() → bonus item masuk cart (price=0, is_bonus_item=true)
  │
  ├─ Pilih Metode Pembayaran
  │    ├─ Cash → input jumlah bayar manual
  │    ├─ QRIS → jumlah bayar otomatis = total
  │    ├─ Transfer → pilih bank (BCA/BRI/BNI/Mandiri/Lainnya)
  │    ├─ E-Wallet → pilih provider (DANA/OVO/GoPay/ShopeePay/Lainnya)
  │    ├─ Debit Card → jumlah bayar otomatis = total
  │    └─ Credit Card → jumlah bayar otomatis = total
  │
  └─ Klik Bayar → processPayment()
```

### Validasi Frontend (processPayment)

1. Cart tidak boleh kosong
2. Item Premium wajib ada bonus item di cart
3. Metode pembayaran wajib dipilih
4. Jumlah bayar >= total

### Payload ke Server

```json
{
  "customer_id": null,
  "customer_type": "retail",
  "items": [
    {
      "product_id": 1,
      "quantity": 1,
      "price": 63000,
      "tier": "premium",
      "bonus_quantity": 0,
      "bonus_note": null,
      "refill_volume_ml": null,
      "is_bonus_item": false,
      "bonus_ml": 0
    },
    {
      "product_id": 5,
      "quantity": 1,
      "price": 0,
      "tier": "biasa",
      "is_bonus_item": true,
      "bonus_ml": 20
    }
  ],
  "discount_amount": 0,
  "discount_type": "fixed",
  "tax_enabled": true,
  "payment_method": "cash",
  "ewallet_type": null,
  "transfer_type": null,
  "paid_amount": 70000
}
```

### Alur Backend

```
POST /transactions → TransactionController@store
  │
  ├─ StoreTransactionRequest::rules() — validasi field
  ├─ StoreTransactionRequest::after() — validasi stok per tier
  │    └─ Hitung ml bibit dibutuhkan per produk:
  │         Premium 30ml = 20ml, Sedang 30ml = 15ml, Biasa 30ml = 10ml
  │         (lihat docs/RACIKAN.md untuk tabel lengkap)
  │
  ├─ DB::transaction() {
  │    ├─ Hitung subtotal, diskon, pajak, total
  │    ├─ Transaction::create()
  │    ├─ Foreach items:
  │    │    ├─ TransactionDetail::create()
  │    │    └─ Jika track_inventory:
  │    │         ├─ isRefill → adjustRefillStock(refill_volume_ml)
  │    │         ├─ isBonusItem → adjustRefillStock(bonus_ml × qty)
  │    │         ├─ isRegular → adjustRefillStock(porsiMl × qty) [per tier]
  │    │         └─ bonus_quantity > 0 → handleBonusStock()
  │    └─ Award loyalty points jika ada customer
  │  }
  │
  ├─ Broadcast DebtSubmitted jika partial payment
  ├─ Broadcast dashboard update
  └─ Return JSON { transaction_id, invoice_number, total, change }
```

### Pemotongan Stok

Semua pemotongan stok menggunakan `adjustRefillStock()` yang:
- Menggunakan `lockForUpdate()` (pessimistic locking) untuk mencegah race condition
- Mengurangi `current_stock` di tabel `inventories`
- Membuat record di `inventory_movements`
- Throw `RuntimeException` jika stok tidak cukup

---

## Transaksi Grosir (Wholesale)

### URL
`GET /wholesale/orders` → `WholesaleController@index`

### Perbedaan dengan Retail

| Aspek | Retail | Grosir |
|-------|--------|--------|
| Harga | Normal | × 0.9 (diskon 10%) |
| Status order | Langsung `paid` | `pending` → `processing` → `completed` |
| Stok dipotong | Saat transaksi | Saat order `pending` dibuat |
| Stok dikembalikan | Saat void | Saat `cancel()`/`destroy()`/`update()` |

### Alur Stok Grosir

```
store() → potong stok (pending)
  │
  ├─ update() → kembalikan stok lama, potong stok baru
  ├─ cancel() → kembalikan stok
  ├─ destroy() → kembalikan stok
  └─ complete() → tidak potong lagi (sudah dipotong di store())
```

---

## Void Transaksi

`DELETE /transactions/{transaction}` → `TransactionController@destroy`

Mengembalikan semua stok:
- Item refill → `adjustRefillStock(refill_volume_ml, 'restore')`
- Item bonus → `adjustRefillStock(20ml, 'restore')`
- Item reguler → `adjustStock(quantity, 'restore')` (current_stock)
- bonus_quantity → `handleBonusStock('restore')`

---

## Kolom Kunci di Database

| Tabel | Kolom | Keterangan |
|-------|-------|------------|
| `transactions` | `payment_status` | `paid` / `partial` / `pending` |
| `transactions` | `customer_type` | `retail` / `wholesale` |
| `transactions` | `ewallet_type` | DANA/OVO/GoPay/ShopeePay/other |
| `transactions` | `transfer_type` | bca/bri/bni/mandiri/other |
| `transaction_details` | `tier` | premium/sedang/biasa (tidak ada kolom ini — tier dikirim tapi tidak disimpan di detail, hanya dipakai untuk kalkulasi stok) |
| `inventories` | `current_stock` | Stok bibit dalam ml |
| `inventory_movements` | `type` | sale/void/adjustment/receive |

> **Catatan:** Field `tier` tidak tersimpan di `transaction_details`. Jika di
> masa depan perlu laporan per tier, tambahkan kolom `tier` di tabel tersebut
> dan simpan `$item['tier']` saat `TransactionDetail::create()`.
