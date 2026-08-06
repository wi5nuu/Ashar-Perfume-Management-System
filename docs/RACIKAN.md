# Standar Racikan Parfum — APMS

Dokumen ini menjelaskan standar racikan produksi parfum yang digunakan sebagai dasar
kalkulasi pengurangan stok bibit saat transaksi terjadi.

---

## Tabel Standar Racikan (Bibit yang Dipakai dari Stok)

| Ukuran Botol | Premium | Sedang | Biasa (Standar) |
|--------------|---------|--------|-----------------|
| 30ml         | 20ml    | 15ml   | 10ml            |
| 50ml         | 33ml    | 25ml   | 17ml            |
| 100ml        | 65ml    | 50ml   | 33ml            |

> Nilai di atas adalah jumlah **bibit parfum** yang dikurangi dari `current_stock`
> di tabel `inventories` setiap kali satu unit produk terjual.

---

## Detail Rasio Racikan per Kualitas

### 1. Kualitas Premium Original
Rasio Bibit : Absolute = 2:1 s.d. 1.5:1

| Ukuran | Bibit        | Absolute     |
|--------|--------------|--------------|
| 30ml   | 18–20ml      | 10–12ml      |
| 50ml   | 30–33ml      | 17–20ml      |
| 100ml  | 60–65ml      | 35–40ml      |

**Sistem pakai nilai maksimum:** 30ml=20ml, 50ml=33ml, 100ml=65ml

### 2. Kualitas Sedang
Rasio Bibit : Absolute = 1:1

| Ukuran | Bibit        | Absolute     |
|--------|--------------|--------------|
| 30ml   | 13–15ml      | 15–17ml      |
| 50ml   | 22–25ml      | 25–28ml      |
| 100ml  | 45–50ml      | 50–55ml      |

**Sistem pakai nilai maksimum:** 30ml=15ml, 50ml=25ml, 100ml=50ml

### 3. Kualitas Biasa (Standar)
Rasio Bibit : Absolute = 1:2

| Ukuran | Bibit        | Absolute     |
|--------|--------------|--------------|
| 30ml   | 10ml         | 20ml         |
| 50ml   | 16–17ml      | 33–34ml      |
| 100ml  | 30–33ml      | 67–70ml      |

**Sistem pakai nilai maksimum:** 30ml=10ml, 50ml=17ml, 100ml=33ml

---

## Implementasi di Kode

### Kalkulasi porsiMl

Logika ini diimplementasikan identik di **dua tempat**:

#### 1. Validasi Stok — `app/Http/Requests/StoreTransactionRequest.php`

```php
$rawSize = strtolower(preg_replace('/\s+/', '', $product->size ?? '30ml'));
$tier    = $item['tier'] ?? 'biasa';
$porsiMl = match(true) {
    str_contains($rawSize, '100') => match($tier) {
        'premium' => 65, 'sedang' => 50, default => 33
    },
    str_contains($rawSize, '50') => match($tier) {
        'premium' => 33, 'sedang' => 25, default => 17
    },
    default => match($tier) {
        'premium' => 20, 'sedang' => 15, default => 10
    },
};
```

Digunakan untuk **validasi** apakah stok cukup sebelum transaksi diproses.

#### 2. Pemotongan Stok — `app/Http/Controllers/TransactionController.php`

Logika identik dengan di atas, digunakan untuk **memotong stok aktual**
via `adjustRefillStock()` saat transaksi berhasil disimpan.

#### 3. Pengiriman Tier dari Frontend — `resources/views/transactions/create.blade.php`

```js
// Di dalam items: cart.map(item => ({...}))
tier: item.tier || 'biasa',
```

Tier dikirim bersama setiap item dalam payload transaksi ke server.

---

## Kolom Database yang Terlibat

| Tabel         | Kolom           | Keterangan                                      |
|---------------|-----------------|-------------------------------------------------|
| `inventories` | `current_stock` | Stok bibit dalam ml — yang dikurangi saat jual  |
| `inventories` | `bulk_stock_ml` | Kolom lama, tidak dipakai untuk pemotongan stok |
| `products`    | `size`          | Ukuran botol: `'30ml'`, `'50ml'`, `'100ml'`    |
| `transaction_details` | `bonus_ml` | ml bonus yang diberikan gratis                |

---

## Catatan Pengembangan

- `bulk_stock_ml` tidak dipakai untuk pemotongan — controller dan validasi
  keduanya menggunakan `current_stock`.
- Jika di masa depan rasio ingin dikonfigurasi per produk, tambahkan kolom
  `fill_ratio` di tabel `products` dan ganti logika `match()` di atas dengan
  `$product->fill_ratio ?? defaultValue`.
- Bonus 20ml gratis untuk pembelian Premium menggunakan stok produk Biasa
  yang dipilih kasir — ini memotong `current_stock` produk bonus tersebut
  sebesar 20ml via `adjustRefillStock()`.
