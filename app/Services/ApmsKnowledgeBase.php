<?php

namespace App\Services;

class ApmsKnowledgeBase
{
    public static function getAll(): array
    {
        return [

            // ───── KASIR / POS ─────────────────────────────────────────────
            'cara_transaksi' => [
                'keywords' => ['cara transaksi', 'cara jual', 'cara pakai kasir', 'buat transaksi', 'proses penjualan', 'cara input penjualan', 'pakai pos', 'cara buka kasir', 'memulai transaksi', 'transaksi baru'],
                'answer'   => "<strong>Prosedur Transaksi Kasir:</strong><br>1. Buka menu <strong>Kasir / POS</strong><br>2. Pastikan shift sudah dibuka (apabila belum, sistem akan meminta open shift)<br>3. Pilih pelanggan (opsional, untuk akumulasi poin loyalitas)<br>4. Cari produk berdasarkan nama atau barcode, kemudian klik <strong>Tambah ke Keranjang</strong><br>5. Atur kuantitas dan diskon apabila diperlukan<br>6. Pilih metode pembayaran (Tunai / QRIS / Transfer / Kas Bon)<br>7. Masukkan jumlah uang yang diterima<br>8. Klik <strong>Proses Pembayaran</strong> untuk menyelesaikan transaksi<br>9. Struk pembayaran akan tampil secara otomatis dan siap dicetak",
                'url'      => '/transactions/create',
                'label'    => 'Buka Mesin Kasir',
            ],
            'cara_pilih_pelanggan_kasir' => [
                'keywords' => ['pilih pelanggan', 'cari pelanggan kasir', 'pilih member', 'transaksi dengan pelanggan', 'input nama pembeli'],
                'answer'   => "<strong>Prosedur Memilih Pelanggan di Kasir:</strong><br>Pada halaman Kasir, terdapat field pencarian pelanggan. Ketik nama atau nomor HP pelanggan, kemudian pilih dari daftar yang muncul. Pelanggan yang sudah dipilih akan otomatis mendapatkan akumulasi poin loyalitas dari transaksi tersebut. Pelanggan baru dapat ditambahkan langsung melalui menu Pelanggan.",
                'url'      => '/customers',
                'label'    => 'Kelola Pelanggan',
            ],
            'cara_cetak_struk' => [
                'keywords' => ['cetak struk', 'print nota', 'struk tidak keluar', 'tidak bisa cetak', 'printer nota', 'cara print', 'ukuran kertas', 'struk tidak muncul', 'nota pembelian', 'cetak ulang', 'print ulang'],
                'answer'   => "<strong>Prosedur Cetak Struk / Nota:</strong><br>1. Pastikan printer telah terhubung ke komputer kasir dan dalam keadaan aktif<br>2. Periksa ukuran kertas di <strong>Pengaturan - Printer</strong> (pilih 58 mm atau 80 mm sesuai spesifikasi printer yang digunakan)<br>3. Setelah transaksi selesai, klik tombol <strong>Cetak Struk</strong> yang muncul di layar<br>4. Untuk mencetak ulang, buka halaman Riwayat Transaksi, cari transaksi yang diinginkan, kemudian klik tombol Cetak Ulang<br><br><strong>Catatan:</strong> Apabila struk tidak keluar, periksa: (1) koneksi printer, (2) ukuran kertas sesuai pengaturan, (3) driver printer terinstal dengan benar.",
                'url'      => '/settings',
                'label'    => 'Atur Ukuran Printer',
            ],
            'cara_diskon_kasir' => [
                'keywords' => ['diskon di kasir', 'kurangi harga', 'potongan harga', 'cashback', 'cara diskon', 'diskon transaksi', 'diskon per item', 'diskon per produk'],
                'answer'   => "<strong>Prosedur Pemberian Diskon di Kasir:</strong><br>Terdapat dua cara memberikan diskon:<br><br><strong>1. Diskon per item:</strong> Setelah menambahkan produk ke keranjang, klik pada baris produk tersebut dan masukkan persentase diskon pada kolom Diskon (%).<br><br><strong>2. Diskon via Kupon:</strong> Di bagian bawah form kasir, masukkan kode kupon yang telah dibuat sebelumnya. Diskon akan diterapkan ke seluruh transaksi.<br><br><strong>Saran:</strong> Buat kupon promo terlebih dahulu melalui menu Kupon &amp; Loyalty agar diskon lebih terstruktur dan dapat dilacak.",
                'url'      => '/coupons',
                'label'    => 'Kelola Kupon Promo',
            ],
            'riwayat_transaksi' => [
                'keywords' => ['riwayat transaksi', 'history penjualan', 'transaksi kemarin', 'lihat transaksi lama', 'rekap jual', 'transaksi yang sudah', 'cari transaksi', 'filter transaksi'],
                'answer'   => "<strong>Melihat Riwayat Transaksi:</strong><br>Seluruh penjualan yang telah diproses tersimpan di halaman <strong>Transaksi</strong>. Anda dapat:<br>- Filter berdasarkan rentang tanggal<br>- Filter berdasarkan kasir yang memproses<br>- Filter berdasarkan status pembayaran (lunas, unpaid, partial)<br>- Filter berdasarkan metode pembayaran (tunai, QRIS, transfer)<br>- Mencari transaksi spesifik menggunakan nomor invoice atau nama pelanggan<br>- Melihat detail setiap transaksi termasuk item yang dibeli",
                'url'      => '/transactions',
                'label'    => 'Buka Riwayat Transaksi',
            ],
            'cara_pembayaran' => [
                'keywords' => ['metode bayar', 'cara bayar', 'pembayaran', 'qris', 'cash', 'tunai', 'transfer', 'debit', 'kredit'],
                'answer'   => "<strong>Metode Pembayaran yang Tersedia:</strong><br>Sistem APMS mendukung beberapa metode pembayaran:<br><br>1. <strong>Tunai (Cash):</strong> Pembayaran langsung menggunakan uang tunai. Sistem akan menghitung kembalian secara otomatis.<br>2. <strong>QRIS:</strong> Pembayaran menggunakan scan QR code dari aplikasi pembayaran (GoPay, OVO, DANA, ShopeePay, dan lain-lain).<br>3. <strong>Transfer:</strong> Pembayaran melalui transfer bank. Nominal dimasukkan manual oleh kasir.<br>4. <strong>Kas Bon / Kredit:</strong> Pembayaran dicicil atau dibayar kemudian. Pelanggan wajib tercatat dalam sistem. Tagihan dapat dilunasi secara bertahap.",
                'url'      => '/transactions/create',
                'label'    => 'Buka Kasir',
            ],

            // ───── PRODUK ───────────────────────────────────────────────────
            'cara_tambah_produk' => [
                'keywords' => ['cara tambah produk', 'tambah barang baru', 'input aroma baru', 'daftarkan produk', 'cara daftar parfum', 'entry produk', 'buat produk', 'produk baru', 'registrasi produk'],
                'answer'   => "<strong>Prosedur Menambah Produk Baru:</strong><br>1. Buka menu <strong>Produk</strong><br>2. Klik tombol <strong>+ Tambah Produk</strong><br>3. Lengkapi data berikut:<br>&nbsp;&nbsp;- Nama Aroma / Nama Produk<br>&nbsp;&nbsp;- Brand (merek parfum)<br>&nbsp;&nbsp;- Ukuran (ml) dan Unit<br>&nbsp;&nbsp;- Harga Jual (eceran)<br>&nbsp;&nbsp;- Harga Grosir (untuk pembelian partai)<br>&nbsp;&nbsp;- Harga Modal (untuk kalkulasi laba)<br>&nbsp;&nbsp;- Supplier (jika ada)<br>4. Unggah foto produk (opsional, direkomendasikan untuk memudahkan identifikasi)<br>5. Klik <strong>Simpan</strong> — produk akan langsung tersedia di halaman Kasir dan Inventory",
                'url'      => '/products',
                'label'    => 'Kelola Produk',
            ],
            'cara_edit_produk' => [
                'keywords' => ['edit produk', 'ubah harga', 'ganti nama barang', 'update produk', 'ubah aroma', 'koreksi produk', 'ubah brand', 'ubah ukuran'],
                'answer'   => "<strong>Prosedur Edit Produk:</strong><br>1. Buka menu <strong>Produk</strong><br>2. Klik ikon pensil (edit) pada baris produk yang ingin diubah<br>3. Ubah data yang diperlukan (harga jual, harga grosir, nama, brand, ukuran, dan lain-lain)<br>4. Klik <strong>Update</strong> untuk menyimpan perubahan<br><br><strong>Informasi:</strong> Perubahan harga akan langsung diterapkan di Kasir tanpa perlu melakukan restart aplikasi. Riwayat perubahan harga tersimpan di log sistem.",
                'url'      => '/products',
                'label'    => 'Buka Daftar Produk',
            ],
            'cara_hapus_produk' => [
                'keywords' => ['hapus produk', 'nonaktifkan produk', 'delete produk', 'arsip produk', 'sembunyikan produk', 'produk tidak dijual'],
                'answer'   => "<strong>Prosedur Menonaktifkan atau Menghapus Produk:</strong><br>1. Buka menu <strong>Produk</strong><br>2. Klik ikon edit pada produk yang ingin dinonaktifkan<br>3. Ubah status menjadi <strong>Nonaktif</strong> atau klik tombol <strong>Arsipkan</strong><br><br><strong>Catatan:</strong> Produk yang sudah memiliki riwayat transaksi tidak dapat dihapus permanen untuk menjaga integritas data. Sebaiknya produk di-nonaktifkan atau diarsipkan saja.",
                'url'      => '/products',
                'label'    => 'Buka Produk',
            ],
            'cara_barcode' => [
                'keywords' => ['barcode', 'cetak label', 'print barcode', 'qrcode', 'kode produk', 'label produk', 'stiker produk', 'scan barcode'],
                'answer'   => "<strong>Prosedur Cetak Barcode Produk:</strong><br>1. Buka menu <strong>Produk</strong><br>2. Pada baris produk yang diinginkan, klik ikon <strong>Barcode</strong><br>3. Halaman cetak barcode akan terbuka — gunakan Ctrl+P untuk mencetak<br>4. Atur orientasi dan ukuran kertas sesuai kebutuhan<br><br><strong>Informasi:</strong> Barcode yang dicetak menggunakan format CODE128 dan dapat dipindai menggunakan scanner barcode pada saat transaksi kasir.",
                'url'      => '/products',
                'label'    => 'Cetak Barcode',
            ],
            'cara_import_produk' => [
                'keywords' => ['import produk', 'upload produk massal', 'tambah banyak produk', 'excel produk', 'csv produk', 'import data'],
                'answer'   => "<strong>Prosedur Import Produk Secara Massal:</strong><br>1. Buka menu <strong>Produk</strong><br>2. Klik tombol <strong>Import</strong><br>3. Download template CSV/Excel yang disediakan<br>4. Isi data produk sesuai format template<br>5. Upload file yang sudah diisi<br>6. Sistem akan memvalidasi dan mengimpor data secara otomatis<br><br><strong>Catatan:</strong> Pastikan format file sesuai dengan template untuk menghindari kesalahan impor.",
                'url'      => '/products',
                'label'    => 'Buka Produk',
            ],

            // ───── INVENTORY / GUDANG ────────────────────────────────────────
            'cara_tambah_stok' => [
                'keywords' => ['tambah stok', 'isi stok', 'restok', 'masukkan barang', 'input gudang', 'update stok', 'cara restock', 'isi gudang', 'tambah inventory', 'adjust stok'],
                'answer'   => "<strong>Prosedur Penambahan atau Penyesuaian Stok Gudang:</strong><br>1. Buka menu <strong>Inventory</strong><br>2. Cari produk yang akan disesuaikan stoknya menggunakan kolom pencarian<br>3. Klik tombol <strong>Adjust Stok</strong> pada baris produk tersebut<br>4. Pilih tipe penyesuaian:<br>&nbsp;&nbsp;- <strong>Tambah (+):</strong> Menambah stok yang sudah ada<br>&nbsp;&nbsp;- <strong>Kurangi (-):</strong> Mengurangi stok yang sudah ada<br>&nbsp;&nbsp;- <strong>Set Langsung:</strong> Mengisi ulang nilai stok (mengganti nilai sebelumnya)<br>5. Masukkan jumlah dan keterangan yang diperlukan (misal: nomor PO, nama supplier)<br>6. Klik <strong>Simpan Perubahan</strong><br><br><strong>Informasi:</strong> Riwayat setiap penyesuaian stok tercatat dalam log inventory dan dapat diaudit.",
                'url'      => '/inventory',
                'label'    => 'Buka Inventory',
            ],
            'cara_set_minimum_stok' => [
                'keywords' => ['minimum stok', 'batas stok', 'alert stok', 'notifikasi stok', 'stok minimal', 'set batas', 'threshold stok', 'peringatan stok'],
                'answer'   => "<strong>Prosedur Mengatur Batas Minimum Stok:</strong><br>Di halaman <strong>Inventory</strong>, setiap produk memiliki kolom <strong>Minimum Stock</strong>. Apabila stok saat ini berada di bawah angka minimum tersebut, sistem akan menandai produk sebagai <strong>Kritis</strong> dan menampilkannya di Dashboard serta laporan Copilot.<br><br><strong>Contoh:</strong> Apabila produk A memiliki minimum stok 10 dan stok saat ini 5, maka produk tersebut akan masuk dalam daftar stok kritis.<br><br>Pengaturan batas minimum dapat disesuaikan per produk berdasarkan kecepatan penjualan masing-masing.",
                'url'      => '/inventory',
                'label'    => 'Atur Minimum Stok',
            ],
            'cara_transfer_stok' => [
                'keywords' => ['transfer stok', 'pindah stok', 'mutasi stok', 'stok antar gudang', 'kirim barang', 'distribusi stok'],
                'answer'   => "<strong>Prosedur Transfer Stok Antar Gudang:</strong><br>1. Buka menu <strong>Inventory - Transfer Stok</strong><br>2. Pilih gudang asal dan gudang tujuan<br>3. Pilih produk yang akan ditransfer<br>4. Masukkan jumlah yang akan dipindahkan<br>5. Klik <strong>Proses Transfer</strong><br><br>Stok akan otomatis berkurang di gudang asal dan bertambah di gudang tujuan. Riwayat transfer tercatat untuk keperluan audit.",
                'url'      => '/inventory',
                'label'    => 'Buka Inventory',
            ],
            'cara_mutasi_stok' => [
                'keywords' => ['mutasi stok', 'riwayat stok', 'log stok', 'pergerakan stok', 'history stok', 'catatan stok'],
                'answer'   => "<strong>Melihat Mutasi / Riwayat Pergerakan Stok:</strong><br>1. Buka menu <strong>Inventory</strong><br>2. Pilih produk yang ingin dilihat riwayatnya<br>3. Klik tombol <strong>Mutasi Stok</strong><br>4. Sistem akan menampilkan seluruh riwayat perubahan stok (penambahan, pengurangan, transfer, penjualan) lengkap dengan tanggal dan petugas<br><br>Fitur ini sangat berguna untuk audit dan investigasi apabila terjadi ketidaksesuaian stok.",
                'url'      => '/inventory',
                'label'    => 'Buka Inventory',
            ],

            // ───── AUDIT STOK ────────────────────────────────────────────────
            'cara_audit_stok' => [
                'keywords' => ['cara audit', 'stock opname', 'hitung fisik', 'cek fisik barang', 'mulai audit', 'buat audit baru', 'cara opname', 'stok opname', 'opname fisik'],
                'answer'   => "<strong>Prosedur Audit Stok (Stock Opname):</strong><br>1. Buka menu <strong>Audit Stok</strong><br>2. Klik <strong>Buat Audit Baru</strong><br>3. Pilih area atau gudang yang akan diaudit<br>4. Pilih produk yang akan dihitung fisiknya<br>5. Hitung fisik parfum di rak atau gudang satu per satu<br>6. Input angka pada kolom <strong>Stok Fisik</strong><br>7. Sistem akan menghitung selisih antara stok sistem dan stok fisik secara otomatis<br>8. Apabila terdapat selisih, sistem akan menyarankan penyesuaian stok<br>9. Isi catatan apabila ditemukan kejanggalan<br>10. Klik <strong>Selesaikan Audit</strong> untuk mengunci hasil<br><br><strong>Peringatan:</strong> Audit yang telah berstatus Selesai tidak dapat diubah kembali. Disarankan melakukan audit secara rutin setiap bulan untuk menjaga akurasi data inventory.",
                'url'      => '/stock_audits',
                'label'    => 'Buka Audit Stok',
            ],
            'cara_selisih_audit' => [
                'keywords' => ['selisih audit', 'beda stok', 'stok tidak cocok', 'kehilangan stok', 'penyusutan', 'over stock', 'under stock'],
                'answer'   => "<strong>Penanganan Selisih Audit Stok:</strong><br>Apabila ditemukan selisih antara stok sistem dan stok fisik, langkah yang disarankan:<br>1. Lakukan penghitungan ulang pada produk yang berselisih<br>2. Periksa riwayat transaksi apakah ada yang terlewat<br>3. Periksa riwayat mutasi stok apakah ada transfer yang belum tercatat<br>4. Apabila selisih memang terjadi, lakukan penyesuaian stok melalui tombol <strong>Adjust Stok</strong><br>5. Catat penyebab selisih untuk evaluasi operasional",
                'url'      => '/stock_audits',
                'label'    => 'Buka Audit Stok',
            ],

            // ───── GROSIR ───────────────────────────────────────────────────
            'cara_pesanan_grosir' => [
                'keywords' => ['cara grosir', 'buat pesanan grosir', 'input pesanan agen', 'pesanan partai', 'cara order grosir', 'invoice grosir', 'cara wholesale', 'pesanan grosir baru'],
                'answer'   => "<strong>Prosedur Membuat Pesanan Grosir:</strong><br>1. Buka menu <strong>Manajemen Grosir - Buat Pesanan</strong><br>2. Pilih pelanggan grosir atau agen dari daftar<br>3. Pilih produk dari katalog (harga grosir akan terisi secara otomatis sesuai pengaturan di master produk)<br>4. Isi kuantitas untuk setiap produk yang dipesan<br>5. Lengkapi data pengiriman: alamat, kurir, estimasi pengiriman<br>6. Masukkan <strong>Target Nilai Paket</strong> (contoh: Rp 10.000.000)<br>7. Sistem akan menghitung total pesanan secara otomatis<br>8. Klik <strong>Simpan</strong> — pesanan akan masuk dengan status <strong>Pending</strong><br>9. Admin melakukan konfirmasi, kemudian stok gudang akan terpotong secara otomatis<br>10. Invoice dapat dicetak dan dibagikan kepada pelanggan",
                'url'      => '/wholesale/create',
                'label'    => 'Buat Pesanan Grosir',
            ],
            'konfirmasi_grosir' => [
                'keywords' => ['konfirmasi grosir', 'approve pesanan', 'proses grosir', 'setujui pesanan', 'cara konfirmasi', 'verifikasi grosir'],
                'answer'   => "<strong>Prosedur Konfirmasi Pesanan Grosir:</strong><br>1. Buka menu <strong>Manajemen Grosir</strong><br>2. Cari pesanan dengan status <strong>Pending</strong><br>3. Klik tombol <strong>Konfirmasi</strong> (hanya dapat dilakukan oleh Admin atau Owner)<br>4. Sistem akan memotong stok secara otomatis dan status berubah menjadi <strong>On Progress</strong><br>5. Apabila pesanan sudah siap dikirim, klik <strong>Siap Kirim</strong> untuk mengubah status dan membuat nomor invoice resmi<br>6. Invoice dapat di-download dalam format PDF atau dibagikan melalui WhatsApp menggunakan tombol yang tersedia<br><br><strong>Catatan:</strong> Setelah status berubah menjadi Siap Kirim, stok sudah tidak dapat dikembalikan secara otomatis. Pembatalan harus dilakukan manual oleh Owner.",
                'url'      => '/wholesale',
                'label'    => 'Lihat Pesanan Grosir',
            ],
            'cara_invoice_grosir' => [
                'keywords' => ['invoice grosir', 'faktur grosir', 'nota grosir', 'cetak invoice grosir', 'download invoice'],
                'answer'   => "<strong>Prosedur Cetak Invoice Grosir:</strong><br>1. Buka menu <strong>Manajemen Grosir</strong><br>2. Cari pesanan yang sudah dikonfirmasi<br>3. Klik tombol <strong>Invoice</strong> atau <strong>Cetak Faktur</strong><br>4. Invoice dalam format PDF akan terbuka<br>5. Gunakan Ctrl+P untuk mencetak atau simpan sebagai PDF<br><br>Invoice grosir mencakup: nomor invoice, data pelanggan, daftar produk, harga, PPN, dan total keseluruhan.",
                'url'      => '/wholesale',
                'label'    => 'Lihat Pesanan Grosir',
            ],
            'cara_kirim_grosir' => [
                'keywords' => ['kirim grosir', 'pengiriman', 'kurir', 'resi', 'packing list', 'surat jalan', 'pengiriman barang'],
                'answer'   => "<strong>Prosedur Pengiriman Pesanan Grosir:</strong><br>1. Setelah pesanan dikonfirmasi, siapkan barang sesuai daftar pesanan<br>2. Gunakan fitur <strong>Packing List</strong> untuk memastikan semua item tersedia<br>3. Input nomor resi pengiriman (apabila menggunakan jasa kurir)<br>4. Status pesanan dapat diubah menjadi <strong>Dikirim</strong><br>5. Informasi resi akan tampil di halaman pesanan untuk dipantau oleh pelanggan",
                'url'      => '/wholesale',
                'label'    => 'Lihat Pesanan Grosir',
            ],
            'batal_grosir' => [
                'keywords' => ['batalkan grosir', 'cancel grosir', 'batal pesanan', 'pembatalan', 'cancel order'],
                'answer'   => "<strong>Prosedur Pembatalan Pesanan Grosir:</strong><br>1. Buka menu <strong>Manajemen Grosir</strong><br>2. Cari pesanan yang akan dibatalkan<br>3. Klik tombol <strong>Batalkan Pesanan</strong><br>4. Sistem akan mengembalikan stok yang sudah terpotong (apabila pesanan sudah dikonfirmasi)<br>5. Masukkan alasan pembatalan untuk dokumentasi<br><br><strong>Catatan:</strong> Pembatalan hanya dapat dilakukan oleh Admin atau Owner.",
                'url'      => '/wholesale',
                'label'    => 'Lihat Pesanan Grosir',
            ],
            'harga_grosir_bulk' => [
                'keywords' => ['harga grosir', 'harga partai', 'bulk price', 'harga volume', 'harga khusus agen', 'pricelist grosir'],
                'answer'   => "<strong>Pengaturan Harga Grosir / Bulk Price:</strong><br>Sistem APMS mendukung penetapan harga khusus untuk pembelian dalam jumlah besar. Pengaturan dapat dilakukan melalui:<br>1. <strong>Master Produk:</strong> Setiap produk memiliki kolom Harga Grosir yang akan otomatis terpakai di menu Grosir<br>2. <strong>Bulk Price:</strong> Pengaturan harga khusus berdasarkan volume pembelian (contoh: pembelian 10-20 pcs diskon 5%, 21-50 pcs diskon 10%)<br>3. <strong>Supplier Price:</strong> Harga khusus dari supplier tertentu<br><br>Pengaturan ini memudahkan dalam memberikan penawaran harga yang berbeda untuk setiap level pelanggan.",
                'url'      => '/wholesale',
                'label'    => 'Kelola Grosir',
            ],

            // ───── SHIFT & KASIR ─────────────────────────────────────────────
            'cara_open_shift' => [
                'keywords' => ['open shift', 'buka shift', 'mulai kerja', 'buka toko', 'cara shift', 'mulai shift', 'cara buka', 'awal shift', 'buka kasir', 'mulai kasir'],
                'answer'   => "<strong>Prosedur Open Shift Kasir:</strong><br>1. Buka menu <strong>Kasir / POS</strong><br>2. Apabila belum ada shift yang aktif, sistem akan menampilkan form Open Shift<br>3. Masukkan <strong>Modal Awal Kas</strong> (jumlah uang yang tersedia di laci kas pada awal shift)<br>4. Konfirmasi nominal modal yang dimasukkan<br>5. Klik <strong>Mulai Shift</strong> — sistem siap mencatat transaksi<br><br><strong>Informasi:</strong> Shift yang sudah aktif akan tercatat dengan waktu mulai, petugas kasir, dan modal awal. Hanya satu shift yang dapat aktif dalam satu waktu.",
                'url'      => '/transactions/create',
                'label'    => 'Buka POS / Open Shift',
            ],
            'cara_closing_kasir' => [
                'keywords' => ['closing kasir', 'tutup shift', 'akhir hari', 'laporan shift', 'cara closing', 'tutup toko', 'end shift', 'close shift', 'selesaikan shift', 'setoran'],
                'answer'   => "<strong>Prosedur Closing / Tutup Shift:</strong><br>1. Buka menu <strong>Shift &amp; Closing Kasir</strong><br>2. Klik pada shift yang sedang aktif untuk melihat detailnya<br>3. Hitung uang yang ada di laci kasir secara fisik<br>4. Masukkan jumlah uang yang dihitung ke dalam sistem<br>5. Sistem akan menghitung selisih antara setoran dan penjualan secara otomatis:<br>&nbsp;&nbsp;- Apabila sesuai: status OK<br>&nbsp;&nbsp;- Apabila kurang: selisih minus<br>&nbsp;&nbsp;- Apabila lebih: selisih plus<br>6. Unggah foto kas (apabila diperlukan untuk dokumentasi)<br>7. Klik <strong>Selesaikan Shift</strong> untuk mengunci laporan shift<br><br>Laporan shift akan mencakup: total penjualan, jumlah transaksi, modal awal, setoran akhir, dan selisih.",
                'url'      => '/shifts',
                'label'    => 'Buka Shift & Closing',
            ],
            'laporan_shift' => [
                'keywords' => ['laporan shift', 'rekap kasir', 'performa kasir', 'shift report', 'total shift', 'ringkasan shift'],
                'answer'   => "<strong>Melihat Laporan Shift Kasir:</strong><br>Halaman <strong>Shift &amp; Closing</strong> menampilkan seluruh riwayat shift yang pernah dilakukan. Anda dapat melihat:<br>- Shift yang sedang aktif (apabila ada)<br>- Riwayat shift yang sudah ditutup<br>- Detail setiap shift: kasir, waktu buka/tutup, modal awal, penjualan, setoran<br>- Selisih kas per shift<br>- Total penjualan per shift<br><br>Data ini berguna untuk evaluasi kinerja kasir dan rekonsiliasi kas harian.",
                'url'      => '/shifts',
                'label'    => 'Buka Shift & Closing',
            ],

            // ───── PELANGGAN / MEMBER ─────────────────────────────────────────
            'cara_daftar_pelanggan' => [
                'keywords' => ['daftar pelanggan', 'tambah member', 'input pelanggan baru', 'cara tambah customer', 'registrasi member', 'buat akun pelanggan', 'pelanggan baru', 'member baru'],
                'answer'   => "<strong>Prosedur Mendaftarkan Pelanggan Baru:</strong><br>1. Buka menu <strong>Pelanggan</strong><br>2. Klik <strong>+ Tambah Pelanggan</strong><br>3. Isi data berikut:<br>&nbsp;&nbsp;- Nama lengkap<br>&nbsp;&nbsp;- Nomor HP (penting untuk program loyalitas dan komunikasi)<br>&nbsp;&nbsp;- Alamat (opsional)<br>&nbsp;&nbsp;- Grup pelanggan / segmentasi (opsional)<br>4. Klik <strong>Simpan</strong><br><br><strong>Informasi:</strong> Pelanggan yang terdaftar dapat langsung dipilih saat transaksi kasir, dan poin loyalitas akan terakumulasi secara otomatis pada setiap transaksi.",
                'url'      => '/customers',
                'label'    => 'Kelola Pelanggan',
            ],
            'cara_cari_pelanggan' => [
                'keywords' => ['cari pelanggan', 'cari member', 'data pelanggan', 'nomor hp pelanggan', 'temukan customer', 'pelanggan berdasarkan', 'filter pelanggan'],
                'answer'   => "<strong>Prosedur Mencari Pelanggan:</strong><br>Di halaman <strong>Pelanggan</strong>, gunakan kolom pencarian yang tersedia. Anda dapat mencari berdasarkan:<br>- <strong>Nama</strong> pelanggan<br>- <strong>Nomor HP</strong> pelanggan<br>- <strong>Kode member</strong><br><br>Hasil pencarian akan tampil secara real-time. Anda juga dapat memfilter pelanggan berdasarkan grup atau tanggal pendaftaran.",
                'url'      => '/customers',
                'label'    => 'Buka Data Pelanggan',
            ],
            'program_loyalitas' => [
                'keywords' => ['poin loyalitas', 'reward', 'loyalty point', 'program member', 'poin belanja', 'akumulasi poin', 'tukar poin'],
                'answer'   => "<strong>Program Loyalitas Pelanggan:</strong><br>Sistem APMS menyediakan program loyalitas berbasis poin. Setiap transaksi yang dilakukan oleh pelanggan terdaftar akan mengakumulasi poin secara otomatis.<br><br>Aturan poin:<br>- Poin dihitung berdasarkan total belanja (dapat dikonfigurasi di Pengaturan)<br>- Poin dapat ditukarkan dengan diskon atau produk tertentu<br>- Riwayat poin setiap pelanggan dapat dilihat di halaman detail pelanggan<br><br>Pengaturan program loyalitas dapat disesuaikan melalui menu Kupon &amp; Loyalty.",
                'url'      => '/coupons',
                'label'    => 'Kelola Loyalty',
            ],

            // ───── KAS BON / UTANG ───────────────────────────────────────────
            'cara_kasbon' => [
                'keywords' => ['cara kasbon', 'input utang', 'catat hutang', 'cara bon', 'buat kas bon', 'cara piutang', 'pelanggan belum bayar', 'kredit', 'bayar nanti'],
                'answer'   => "<strong>Prosedur Pencatatan Kas Bon (Piutang):</strong><br>1. Pada saat melakukan transaksi kasir, pilih metode pembayaran <strong>Kas Bon / Kredit</strong><br>2. Sistem akan mencatat transaksi tersebut sebagai piutang atas nama pelanggan yang dipilih<br>3. Pelanggan wajib dipilih dari daftar pelanggan yang sudah terdaftar<br>4. Transaksi akan memiliki status pembayaran <strong>Unpaid</strong> atau <strong>Partial</strong> (apabila ada pembayaran awal)<br>5. Untuk melihat seluruh tagihan yang belum dilunasi, buka menu <strong>Manajemen Kas Bon</strong><br><br><strong>Informasi:</strong> Sistem akan menampilkan status pembayaran, sisa tagihan, dan riwayat pembayaran untuk setiap transaksi kredit.",
                'url'      => '/debts',
                'label'    => 'Buka Buku Kas Bon',
            ],
            'cara_bayar_kasbon' => [
                'keywords' => ['bayar bon', 'lunasi hutang', 'pelanggan bayar', 'cicil bon', 'terima bayar kasbon', 'konfirmasi bayar', 'pembayaran piutang', 'pelunasan'],
                'answer'   => "<strong>Prosedur Penerimaan Pembayaran Kas Bon:</strong><br>1. Buka menu <strong>Manajemen Kas Bon</strong><br>2. Cari nama pelanggan atau transaksi yang akan melakukan pembayaran<br>3. Klik tombol <strong>Terima Pembayaran</strong><br>4. Masukkan jumlah yang dibayarkan (pembayaran dapat dilakukan secara bertahap / cicilan)<br>5. Pilih metode pembayaran (Tunai, QRIS, Transfer)<br>6. Klik <strong>Konfirmasi Pembayaran</strong><br>7. Sistem akan menghitung sisa tagihan secara otomatis dan mengubah status pembayaran menjadi <strong>Partial</strong> (apabila masih ada sisa) atau <strong>Paid</strong> (apabila lunas)<br><br>Riwayat pembayaran tercatat lengkap dengan tanggal, jumlah, dan petugas yang menerima.",
                'url'      => '/debts',
                'label'    => 'Kelola Kas Bon',
            ],
            'laporan_piutang' => [
                'keywords' => ['laporan piutang', 'rekap piutang', 'daftar tagihan', 'total piutang', 'aging piutang'],
                'answer'   => "<strong>Laporan Piutang / Kas Bon:</strong><br>Halaman Manajemen Kas Bon menyediakan informasi lengkap tentang piutang, meliputi:<br>- Total piutang outstanding<br>- Daftar pelanggan yang memiliki tagihan<br>- Status pembayaran (Unpaid / Partial / Paid)<br>- Sisa tagihan per pelanggan<br>- Riwayat pembayaran<br><br>Data ini sangat penting untuk manajemen arus kas dan pengingat tagihan kepada pelanggan.",
                'url'      => '/debts',
                'label'    => 'Buka Kas Bon',
            ],

            // ───── PENGELUARAN ───────────────────────────────────────────────
            'cara_input_pengeluaran' => [
                'keywords' => ['input pengeluaran', 'catat pengeluaran', 'tambah biaya', 'cara expense', 'catat belanja', 'biaya operasional', 'catat biaya', 'pengeluaran baru'],
                'answer'   => "<strong>Prosedur Pencatatan Pengeluaran / Biaya:</strong><br>1. Buka menu <strong>Pengeluaran</strong><br>2. Klik <strong>+ Tambah Pengeluaran</strong><br>3. Isi data berikut:<br>&nbsp;&nbsp;- <strong>Kategori:</strong> Pilih kategori pengeluaran (Sewa, Listrik, Air, Gaji, Stok, Operasional, dan lain-lain)<br>&nbsp;&nbsp;- <strong>Jumlah:</strong> Nominal pengeluaran<br>&nbsp;&nbsp;- <strong>Deskripsi:</strong> Keterangan pengeluaran<br>&nbsp;&nbsp;- <strong>Tanggal:</strong> Tanggal pengeluaran terjadi<br>&nbsp;&nbsp;- <strong>Metode Pembayaran:</strong> Tunai, QRIS, atau Transfer<br>4. Klik <strong>Simpan</strong><br><br><strong>Informasi:</strong> Data pengeluaran akan secara otomatis diperhitungkan dalam Laporan Laba Rugi, sehingga Anda dapat melihat keuntungan bersih secara akurat.",
                'url'      => '/expenses',
                'label'    => 'Kelola Pengeluaran',
            ],
            'kategori_pengeluaran' => [
                'keywords' => ['kategori pengeluaran', 'jenis biaya', 'kelompok expense', 'klasifikasi biaya'],
                'answer'   => "<strong>Kategori Pengeluaran yang Tersedia:</strong><br>Sistem APMS menyediakan beberapa kategori pengeluaran standar:<br>1. Sewa tempat<br>2. Listrik dan air<br>3. Gaji karyawan<br>4. Pembelian stok barang<br>5. Biaya operasional (ATK, kebersihan, dan lain-lain)<br>6. Biaya pemasaran dan promosi<br>7. Biaya pengiriman<br>8. Biaya perawatan dan perbaikan<br>9. Biaya administrasi<br>10. Lain-lain<br><br>Kategori dapat disesuaikan dengan kebutuhan toko melalui menu Pengeluaran.",
                'url'      => '/expenses',
                'label'    => 'Kelola Pengeluaran',
            ],

            // ───── LAPORAN ──────────────────────────────────────────────────
            'cara_laporan_penjualan' => [
                'keywords' => ['laporan penjualan', 'rekap omzet', 'laporan harian', 'laporan bulanan', 'export laporan', 'download laporan', 'cetak laporan', 'lihat laporan', 'rekap transaksi'],
                'answer'   => "<strong>Prosedur Melihat dan Export Laporan Penjualan:</strong><br>1. Buka menu <strong>Laporan</strong><br>2. Pilih tab laporan yang diinginkan:<br>&nbsp;&nbsp;- <strong>Penjualan Harian:</strong> Rekap transaksi per hari<br>&nbsp;&nbsp;- <strong>Penjualan Bulanan:</strong> Grafik dan data penjualan bulanan<br>&nbsp;&nbsp;- <strong>Produk Terlaris:</strong> Top produk berdasarkan volume penjualan<br>&nbsp;&nbsp;- <strong>Laba Rugi:</strong> Pendapatan dikurangi pengeluaran dan modal<br>&nbsp;&nbsp;- <strong>Rekap Piutang:</strong> Status tagihan pelanggan<br>&nbsp;&nbsp;- <strong>Laporan Shift:</strong> Rekap per shift kasir<br>3. Atur rentang tanggal sesuai kebutuhan<br>4. Klik <strong>Export PDF</strong> atau <strong>Export CSV</strong> untuk mengunduh data<br><br><strong>Informasi:</strong> Laporan Laba Rugi telah memperhitungkan pengeluaran secara otomatis dari total omzet, sehingga memberikan gambaran keuntungan bersih yang akurat.",
                'url'      => '/reports',
                'label'    => 'Buka Laporan',
            ],
            'cara_laporan_laba_rugi' => [
                'keywords' => ['laba rugi', 'profit loss', 'keuntungan bersih', 'net profit', 'laba kotor', 'margin keuntungan', 'pendapatan bersih'],
                'answer'   => "<strong>Laporan Laba Rugi:</strong><br>Laporan Laba Rugi di APMS menyajikan informasi keuangan secara komprehensif:<br><br>1. <strong>Total Pendapatan (Omzet):</strong> Seluruh penjualan dalam periode tertentu<br>2. <strong>Total Pengeluaran:</strong> Seluruh biaya yang dicatat dalam periode yang sama<br>3. <strong>Laba Kotor:</strong> Pendapatan dikurangi harga modal produk<br>4. <strong>Laba Bersih:</strong> Laba kotor dikurangi pengeluaran operasional<br><br>Laporan ini penting untuk mengevaluasi kesehatan keuangan bisnis dan dapat diexport dalam format PDF atau CSV.",
                'url'      => '/reports',
                'label'    => 'Buka Laporan',
            ],
            'cara_grafik_analitik' => [
                'keywords' => ['grafik', 'chart', 'analitik', 'analisis data', 'tren penjualan', 'visualisasi', 'dashboard analitik'],
                'answer'   => "<strong>Fitur Grafik dan Analitik:</strong><br>Dashboard Laporan APMS menyediakan berbagai grafik dan analitik untuk memudahkan pemantauan bisnis, meliputi:<br>1. <strong>Grafik Penjualan Harian:</strong> Menampilkan tren penjualan harian dalam bentuk line chart<br>2. <strong>Distribusi Pembayaran:</strong> Diagram lingkaran (donut) dan diagram batang yang menunjukkan proporsi metode pembayaran<br>3. <strong>Periode Comparison:</strong> Perbandingan penjualan antar periode untuk melihat pertumbuhan<br>4. <strong>Produk Terlaris:</strong> Diagram batang produk dengan penjualan tertinggi<br>5. <strong>Ringkasan Keuangan:</strong> Total pendapatan, pengeluaran, dan laba bersih<br><br>Semua grafik bersifat interaktif dan dapat difilter berdasarkan rentang tanggal.",
                'url'      => '/reports',
                'label'    => 'Buka Laporan',
            ],
            'produk_terlaris' => [
                'keywords' => ['produk terlaris', 'aroma terlaris', 'barang paling laku', 'best seller', 'paling sering dibeli', 'populer', 'top sales', 'ranking produk'],
                'answer'   => null,
                'dynamic'  => 'top_products',
            ],

            // ───── KARYAWAN & ABSENSI ────────────────────────────────────────
            'cara_tambah_karyawan' => [
                'keywords' => ['tambah karyawan', 'daftar karyawan baru', 'input pegawai', 'buat akun kasir', 'kasir baru', 'create user', 'tambah user', 'registrasi karyawan'],
                'answer'   => "<strong>Prosedur Penambahan Karyawan atau Kasir Baru:</strong><br>1. Buka menu <strong>Karyawan</strong><br>2. Klik <strong>+ Tambah Karyawan</strong><br>3. Isi data berikut:<br>&nbsp;&nbsp;- Nama lengkap<br>&nbsp;&nbsp;- Email (digunakan untuk login)<br>&nbsp;&nbsp;- Role / Hak Akses<br>&nbsp;&nbsp;- Password (minimal 8 karakter)<br>4. Klik <strong>Simpan</strong><br><br><strong>Informasi Hak Akses:</strong><br>- <strong>Kasir:</strong> Hanya dapat mengakses POS / Kasir<br>- <strong>Admin:</strong> Dapat mengelola produk, inventory, pelanggan, dan laporan<br>- <strong>Manager:</strong> Akses hampir seluruh fitur kecuali pengaturan sistem<br>- <strong>Owner:</strong> Memiliki akses penuh terhadap seluruh fitur sistem termasuk pengaturan dan backup",
                'url'      => '/employees',
                'label'    => 'Kelola Karyawan',
            ],
            'cara_edit_karyawan' => [
                'keywords' => ['edit karyawan', 'ubah role', 'ganti password', 'reset password karyawan', 'ubah data karyawan', 'nonaktifkan karyawan'],
                'answer'   => "<strong>Prosedur Edit Data Karyawan:</strong><br>1. Buka menu <strong>Karyawan</strong><br>2. Cari karyawan yang akan diedit<br>3. Klik ikon edit pada baris karyawan tersebut<br>4. Ubah data yang diperlukan (nama, email, role, status aktif)<br>5. Untuk mereset password, klik tombol <strong>Reset Password</strong><br>6. Klik <strong>Update</strong> untuk menyimpan perubahan<br><br>Karyawan yang dinonaktifkan tidak dapat login ke sistem sampai diaktifkan kembali oleh Admin atau Owner.",
                'url'      => '/employees',
                'label'    => 'Kelola Karyawan',
            ],
            'cara_absensi' => [
                'keywords' => ['cara absen', 'input absensi', 'check in karyawan', 'record kehadiran', 'catat hadir', 'absensi hari ini', 'absen kerja', 'check in', 'check out'],
                'answer'   => "<strong>Prosedur Input Absensi Kehadiran:</strong><br>1. Buka menu <strong>Absensi Kehadiran</strong><br>2. Klik tombol <strong>Check In</strong> untuk menandai mulai bekerja<br>3. Sistem akan mencatat waktu check in secara otomatis<br>4. Pada saat selesai bekerja, klik <strong>Check Out</strong><br>5. Sistem akan menghitung total jam kerja secara otomatis<br><br><strong>Informasi:</strong> Rekapitulasi absensi bulanan tersedia untuk keperluan perhitungan gaji dan evaluasi kehadiran. Data absensi dapat diexport dalam format Excel.",
                'url'      => '/attendances',
                'label'    => 'Buka Absensi',
            ],
            'rekap_absensi' => [
                'keywords' => ['rekap absensi', 'laporan kehadiran', 'total hadir', 'absensi bulanan', 'daftar hadir', 'presentase kehadiran'],
                'answer'   => "<strong>Rekap Absensi Bulanan:</strong><br>Halaman Absensi Kehadiran menyediakan rekapitulasi kehadiran karyawan yang meliputi:<br>- Total hari kerja per karyawan<br>- Total jam kerja per karyawan<br>- Keterlambatan (jika ada)<br>- Riwayat check in dan check out harian<br><br>Data ini dapat difilter berdasarkan bulan dan tahun, serta dapat diexport untuk keperluan penggajian.",
                'url'      => '/attendances',
                'label'    => 'Buka Absensi',
            ],

            // ───── KUPON & LOYALTY ───────────────────────────────────────────
            'cara_buat_kupon' => [
                'keywords' => ['cara buat kupon', 'buat promo', 'input kode diskon', 'tambah voucher', 'cara voucher', 'cara kode promo', 'diskon kupon', 'promo diskon'],
                'answer'   => "<strong>Prosedur Pembuatan Kupon Promo:</strong><br>1. Buka menu <strong>Kupon &amp; Loyalty</strong><br>2. Klik <strong>+ Tambah Kupon</strong><br>3. Isi data berikut:<br>&nbsp;&nbsp;- <strong>Kode Unik:</strong> Kode yang akan dimasukkan kasir (contoh: PROMO10)<br>&nbsp;&nbsp;- <strong>Tipe Diskon:</strong> Persentase (%) atau Nominal (Rp)<br>&nbsp;&nbsp;- <strong>Jumlah Diskon:</strong> Nilai diskon (contoh: 10 untuk 10%, atau 50000 untuk Rp 50.000)<br>&nbsp;&nbsp;- <strong>Batas Pemakaian:</strong> Maksimal berapa kali kupon dapat digunakan<br>&nbsp;&nbsp;- <strong>Minimal Belanja:</strong> Syarat minimal transaksi (opsional)<br>&nbsp;&nbsp;- <strong>Tanggal Berlaku:</strong> Periode validitas kupon<br>4. Klik <strong>Simpan</strong><br><br><strong>Informasi:</strong> Kode kupon dapat langsung digunakan oleh kasir pada saat melayani pelanggan dengan memasukkannya di bagian bawah form kasir.",
                'url'      => '/coupons',
                'label'    => 'Buat Kupon Promo',
            ],
            'cara_kelola_kupon' => [
                'keywords' => ['kelola kupon', 'edit kupon', 'nonaktifkan kupon', 'hapus kupon', 'lihat kupon aktif'],
                'answer'   => "<strong>Prosedur Mengelola Kupon:</strong><br>Di halaman Kupon &amp; Loyalty, Anda dapat:<br>- Melihat daftar semua kupon (aktif dan tidak aktif)<br>- Mengedit kupon yang sudah ada<br>- Menonaktifkan kupon yang sudah tidak berlaku<br>- Melihat statistik pemakaian setiap kupon (berapa kali digunakan, total diskon yang diberikan)<br><br>Kupon yang sudah melewati tanggal berlaku akan otomatis tidak dapat digunakan.",
                'url'      => '/coupons',
                'label'    => 'Kelola Kupon',
            ],

            // ───── PENGATURAN ────────────────────────────────────────────────
            'cara_ganti_logo' => [
                'keywords' => ['ganti logo', 'upload logo', 'logo toko', 'foto toko', 'branding', 'identitas toko', 'ubah logo', 'setting logo'],
                'answer'   => "<strong>Prosedur Mengganti Logo Toko:</strong><br>1. Buka menu <strong>Pengaturan - Identitas Toko</strong><br>2. Klik tombol <strong>Upload Logo</strong> atau area gambar logo saat ini<br>3. Pilih file gambar dari komputer (format: PNG, JPG, maksimal 2 MB)<br>4. Pratinjau (preview) akan tampil secara langsung<br>5. Klik <strong>Simpan Pengaturan</strong><br><br><strong>Informasi:</strong> Logo toko akan tampil di sidebar aplikasi, pada struk/nota cetak, serta pada invoice grosir.",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],
            'cara_ganti_nama_toko' => [
                'keywords' => ['ganti nama toko', 'ubah nama toko', 'nama perusahaan', 'identitas perusahaan', 'profil toko'],
                'answer'   => "<strong>Prosedur Mengubah Identitas Toko:</strong><br>1. Buka menu <strong>Pengaturan - Identitas Toko</strong><br>2. Ubah data yang diperlukan: Nama Toko, Alamat, Nomor Telepon, NPWP, dan informasi lainnya<br>3. Klik <strong>Simpan Pengaturan</strong><br><br>Informasi ini akan muncul di seluruh dokumen cetak seperti struk, nota, invoice, dan faktur pajak.",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],
            'cara_backup' => [
                'keywords' => ['cara backup', 'backup data', 'simpan database', 'export database', 'download backup', 'amankan data', 'cadangkan data'],
                'answer'   => "<strong>Prosedur Backup Database:</strong><br>1. Buka menu <strong>Pengaturan - Sistem &amp; Backup</strong><br>2. Klik tombol <strong>Backup Database</strong><br>3. Tunggu proses backup (beberapa detik tergantung ukuran data)<br>4. File dengan ekstensi <strong>.sql</strong> akan terunduh ke komputer Anda<br><br><strong>Rekomendasi:</strong> Lakukan backup secara rutin setiap minggu dan simpan di minimal dua tempat berbeda (contoh: hard drive lokal dan Google Drive) untuk keamanan data. Backup sangat penting sebelum melakukan update sistem atau restore data.",
                'url'      => '/settings',
                'label'    => 'Buka Sistem & Backup',
            ],
            'cara_restore' => [
                'keywords' => ['cara restore', 'pulihkan data', 'kembalikan data', 'import database', 'upload backup', 'restore database', 'recovery data'],
                'answer'   => "<strong>Prosedur Restore Database dari Backup:</strong><br>1. Buka menu <strong>Pengaturan - Sistem &amp; Backup</strong><br>2. Di bagian <strong>Restore Data</strong>, klik <strong>Pilih File .sql</strong><br>3. Pilih file backup yang pernah diunduh sebelumnya<br>4. Baca peringatan yang ditampilkan dengan saksama<br>5. Konfirmasi restore — data saat ini akan diganti seluruhnya dengan data dari file backup<br><br><strong>Peringatan Penting:</strong><br>- Selalu lakukan backup data terkini SEBELUM menjalankan restore<br>- Proses restore tidak dapat dibatalkan setelah dimulai<br>- Pastikan file backup berasal dari sistem APMS yang sama (versi kompatibel)<br>- Hubungi Owner atau developer apabila ragu",
                'url'      => '/settings',
                'label'    => 'Buka Sistem & Backup',
            ],
            'cara_ubah_pajak' => [
                'keywords' => ['ubah pajak', 'setting ppn', 'ganti ppn', 'pajak toko', 'tarif pajak', 'set pajak', 'ppn', 'pajak pertambahan nilai'],
                'answer'   => "<strong>Prosedur Mengatur Pajak (PPN):</strong><br>1. Buka menu <strong>Pengaturan</strong><br>2. Cari input <strong>Pajak Standard (PPN %)</strong><br>3. Masukkan angka persentase yang sesuai dengan ketentuan yang berlaku (contoh: 11 untuk PPN 11%)<br>4. Klik <strong>Simpan Pengaturan</strong><br><br>Nilai PPN akan otomatis diterapkan pada faktur grosir dan invoice. Pastikan persentase PPN selalu diperbarui sesuai dengan peraturan perpajakan yang berlaku.",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],
            'cara_pengaturan_printer' => [
                'keywords' => ['pengaturan printer', 'setting printer', 'ukuran kertas', 'jenis printer', 'thermal printer', 'setting nota'],
                'answer'   => "<strong>Prosedur Pengaturan Printer:</strong><br>1. Buka menu <strong>Pengaturan - Printer</strong><br>2. Pilih ukuran kertas yang digunakan:<br>&nbsp;&nbsp;- <strong>58 mm:</strong> Untuk printer thermal ukuran kecil (struk kasir standar)<br>&nbsp;&nbsp;- <strong>80 mm:</strong> Untuk printer thermal ukuran besar (nota lebih lebar)<br>3. Atur jumlah copy cetakan (jika diperlukan)<br>4. Klik <strong>Simpan Pengaturan</strong><br><br>Pengaturan ini akan memengaruhi format cetak struk kasir dan nota grosir.",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],

            // ───── SUPPLIER & PURCHASE ORDER ────────────────────────────────
            'cara_po' => [
                'keywords' => ['purchase order', 'po', 'pesanan barang', 'order ke supplier', 'beli barang', 'pembelian stok'],
                'answer'   => "<strong>Prosedur Pembuatan Purchase Order (PO):</strong><br>1. Buka menu <strong>Purchase Order</strong><br>2. Klik <strong>Buat PO Baru</strong><br>3. Pilih supplier yang akan dipesan<br>4. Pilih produk dari daftar (lengkapi dengan kuantitas dan harga beli)<br>5. Sistem akan menghitung total pesanan<br>6. Atur status PO (Draft / Dikirim / Disetujui / Diterima)<br>7. Klik <strong>Simpan</strong><br><br>PO yang sudah diterima akan otomatis menambah stok gudang sesuai dengan produk yang dipesan.",
                'url'      => '/purchase-orders',
                'label'    => 'Buka Purchase Order',
            ],
            'cara_supplier' => [
                'keywords' => ['supplier', 'pemasok', 'vendor', 'pemasok barang', 'daftar supplier', 'tambah supplier', 'kelola supplier'],
                'answer'   => "<strong>Prosedur Manajemen Supplier:</strong><br>1. Buka menu <strong>Supplier / Pemasok</strong><br>2. Klik <strong>+ Tambah Supplier</strong><br>3. Isi data: Nama Supplier, Kontak (nomor HP/telepon), Alamat, Email<br>4. Klik <strong>Simpan</strong><br><br>Data supplier akan digunakan pada saat pembuatan Purchase Order (PO) dan pencatatan harga beli produk. Riwayat pembelian dari setiap supplier dapat dipantau untuk evaluasi kinerja.",
                'url'      => '/suppliers',
                'label'    => 'Kelola Supplier',
            ],

            // ───── RETUR ───────────────────────────────────────────────────
            'cara_retur' => [
                'keywords' => ['retur barang', 'pengembalian', 'return', 'barang rusak', 'barang cacat', 'retur pembelian', 'retur penjualan', 'refund'],
                'answer'   => "<strong>Prosedur Retur Barang:</strong><br>1. Buka menu <strong>Retur</strong><br>2. Klik <strong>Buat Retur Baru</strong><br>3. Pilih jenis retur:<br>&nbsp;&nbsp;- <strong>Retur Penjualan:</strong> Pelanggan mengembalikan barang yang sudah dibeli<br>&nbsp;&nbsp;- <strong>Retur Pembelian:</strong> Mengembalikan barang ke supplier<br>4. Pilih transaksi terkait<br>5. Pilih produk yang diretur dan jumlahnya<br>6. Masukkan alasan retur<br>7. Klik <strong>Proses Retur</strong><br><br>Stok akan otomatis disesuaikan sesuai dengan jenis retur yang dilakukan.",
                'url'      => '/returns',
                'label'    => 'Buka Retur',
            ],

            // ───── TROUBLESHOOTING ───────────────────────────────────────────
            'masalah_login' => [
                'keywords' => ['tidak bisa login', 'lupa password', 'password salah', 'akun terkunci', 'gagal masuk', 'error login', 'login gagal', 'cannot login'],
                'answer'   => "<strong>Penanganan Masalah Login:</strong><br>Apabila Anda mengalami kesulitan masuk ke sistem, lakukan langkah berikut:<br><br>1. Pastikan email dan password yang dimasukkan sudah benar (password bersifat case-sensitive / huruf besar-kecil berpengaruh)<br>2. Periksa apakah Caps Lock dalam keadaan aktif atau tidak<br>3. Lupa password? Klik tautan <strong>Lupa Password</strong> di halaman login — tautan reset akan dikirim ke email terdaftar<br>4. Periksa folder Spam / Junk email apabila tautan reset tidak muncul di Inbox<br>5. Apabila akun diblokir atau terkunci, silakan hubungi Owner untuk mereset akun melalui menu <strong>Karyawan</strong><br><br><strong>Pencegahan:</strong> Selalu logout setelah selesai menggunakan sistem, terutama di perangkat bersama.",
                'url'      => '/employees',
                'label'    => 'Kelola Akun Karyawan',
            ],
            'masalah_stok_minus' => [
                'keywords' => ['stok minus', 'stok negatif', 'stok kurang dari nol', 'stok error', 'stok tidak wajar', 'stok aneh'],
                'answer'   => "<strong>Penanganan Stok Negatif atau Minus:</strong><br>Kondisi stok negatif dapat terjadi apabila sistem digunakan sebelum stok awal diinput dengan benar, atau terdapat transaksi yang tidak tercatat. Langkah perbaikan:<br><br>1. Buka menu <strong>Inventory</strong><br>2. Cari produk yang bernilai minus menggunakan kolom pencarian<br>3. Klik <strong>Adjust Stok</strong> pada produk tersebut<br>4. Pilih tipe penyesuaian <strong>Set Langsung</strong><br>5. Masukkan jumlah stok fisik yang sebenarnya<br>6. Berikan keterangan yang jelas (contoh: Koreksi stok awal)<br>7. Klik <strong>Simpan Perubahan</strong><br><br><strong>Pencegahan:</strong> Lakukan Audit Stok (Stock Opname) secara rutin setiap bulan untuk memastikan keakuratan data inventory. Input stok awal dengan benar sebelum menggunakan sistem untuk transaksi.",
                'url'      => '/inventory',
                'label'    => 'Buka Inventory',
            ],
            'masalah_laporan_kosong' => [
                'keywords' => ['laporan kosong', 'grafik tidak muncul', 'data laporan nol', 'laba rugi kosong', 'report tidak ada', 'data tidak tampil'],
                'answer'   => "<strong>Penanganan Laporan yang Tampak Kosong:</strong><br>Apabila laporan atau grafik tidak menampilkan data, lakukan langkah berikut:<br><br>1. Periksa rentang <strong>tanggal filter</strong> — pastikan pilihan tanggal Dari dan Sampai sudah benar<br>2. Pastikan sudah terdapat transaksi atau data pada periode yang dipilih<br>3. Coba perlebar rentang tanggal untuk melihat apakah data tersedia di periode lain<br>4. Arahkan tim untuk mencatat pengeluaran secara rutin agar Laba Rugi terhitung dengan akurat<br>5. Apabila laporan tetap kosong, coba jalankan perintah melalui terminal: <code>php artisan cache:clear</code> untuk membersihkan cache sistem<br>6. Hubungi developer apabila masalah berlanjut",
                'url'      => '/reports',
                'label'    => 'Buka Laporan',
            ],
            'masalah_harga_salah' => [
                'keywords' => ['harga salah', 'harga berbeda', 'harga tidak sesuai', 'update harga', 'harga lama', 'harga tidak berubah', 'harga belum update'],
                'answer'   => "<strong>Penanganan Harga Produk yang Tidak Sesuai:</strong><br>1. Buka menu <strong>Produk</strong> dan cari produk yang bermasalah<br>2. Klik <strong>Edit</strong> pada produk tersebut<br>3. Periksa dan ubah <strong>Harga Jual</strong> (untuk eceran) dan/atau <strong>Harga Grosir</strong> (untuk partai)<br>4. Klik <strong>Update</strong> untuk menyimpan perubahan<br><br><strong>Informasi:</strong> Perubahan harga akan langsung diterapkan di halaman Kasir tanpa perlu melakukan restart atau refresh aplikasi. Apabila harga di Kasir masih belum berubah, coba refresh halaman Kasir dengan menekan F5.",
                'url'      => '/products',
                'label'    => 'Edit Produk',
            ],
            'masalah_aplikasi_lambat' => [
                'keywords' => ['aplikasi lambat', 'loading lama', 'slow', 'lemot', 'berat', 'loading terus', 'tidak responsif'],
                'answer'   => "<strong>Penanganan Aplikasi Lambat atau Lemot:</strong><br>Apabila sistem berjalan lambat, coba langkah-langkah berikut:<br><br>1. <strong>Clear cache:</strong> Jalankan perintah <code>php artisan cache:clear</code> dan <code>php artisan view:clear</code><br>2. <strong>Periksa koneksi internet:</strong> Pastikan koneksi stabil, terutama apabila menggunakan server cloud<br>3. <strong>Kurangi data tidak perlu:</strong> Lakukan backup kemudian hapus data transaksi lama yang sudah tidak diperlukan<br>4. <strong>Restart server:</strong> Coba restart server atau hosting apabila memungkinkan<br>5. <strong>Hubungi developer:</strong> Apabila masalah berlanjut, kemungkinan perlu optimasi database atau upgrade server",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],
            'masalah_nota_tidak_sesuai' => [
                'keywords' => ['nota salah', 'struk salah', 'format nota', 'tampilan nota', 'nota berantakan', 'struk terpotong'],
                'answer'   => "<strong>Penanganan Masalah Nota / Struk:</strong><br>Apabila tampilan nota atau struk tidak sesuai, periksa hal berikut:<br><br>1. Buka <strong>Pengaturan - Printer</strong><br>2. Pastikan ukuran kertas sudah sesuai (58 mm atau 80 mm)<br>3. Periksa margin dan orientasi cetak<br>4. Lakukan uji coba cetak dengan kertas yang berbeda<br><br>Apabila masih bermasalah, kemungkinan driver printer perlu diperbarui atau diganti.",
                'url'      => '/settings',
                'label'    => 'Buka Pengaturan',
            ],

            // ───── CABANG / BRANCH ──────────────────────────────────────────
            'cara_cabang' => [
                'keywords' => ['cabang', 'branch', 'manajemen cabang', 'tambah cabang', 'buka cabang baru', 'multi cabang', 'outlet'],
                'answer'   => "<strong>Prosedur Manajemen Cabang:</strong><br>1. Buka menu <strong>Cabang / Branch</strong><br>2. Klik <strong>+ Tambah Cabang</strong><br>3. Isi data: Nama Cabang, Alamat, Nomor Telepon<br>4. Klik <strong>Simpan</strong><br><br>Sistem APMS mendukung operasional multi-cabang. Setiap cabang memiliki data transaksi, inventory, dan karyawan yang terpisah. Owner dapat melihat laporan gabungan seluruh cabang maupun per cabang.",
                'url'      => '/branches',
                'label'    => 'Kelola Cabang',
            ],

            // ───── KOMISI KARYAWAN ─────────────────────────────────────────
            'cara_komisi' => [
                'keywords' => ['komisi', 'komisi karyawan', 'bonus', 'insentif', 'komisi penjualan', 'perhitungan komisi'],
                'answer'   => "<strong>Prosedur Manajemen Komisi Karyawan:</strong><br>1. Buka menu <strong>Komisi</strong><br>2. Atur persentase komisi untuk setiap karyawan atau berdasarkan role<br>3. Komisi akan dihitung secara otomatis berdasarkan penjualan yang dilakukan oleh karyawan tersebut<br>4. Laporan komisi dapat dilihat per bulan dan diexport untuk penggajian<br><br>Sistem mendukung perhitungan komisi berdasarkan persentase dari total penjualan atau per produk tertentu.",
                'url'      => '/commissions',
                'label'    => 'Kelola Komisi',
            ],

            // ───── REKONSILIASI KAS ─────────────────────────────────────────
            'cara_rekonsiliasi' => [
                'keywords' => ['rekonsiliasi kas', 'cocokkan kas', 'selisih kas', 'beda kas', 'rekonsiliasi', 'penyesuaian kas'],
                'answer'   => "<strong>Prosedur Rekonsiliasi Kas:</strong><br>1. Buka menu <strong>Rekonsiliasi Kas</strong><br>2. Sistem akan menampilkan perbandingan antara kas fisik dan kas sistem<br>3. Lakukan pengecekan terhadap setiap transaksi yang menimbulkan selisih<br>4. Apabila terdapat selisih yang dapat dijelaskan, buat catatan dan lakukan penyesuaian<br>5. Apabila selisih tidak dapat dijelaskan, laporkan kepada Owner untuk tindak lanjut<br><br>Rekonsiliasi kas sebaiknya dilakukan setiap hari setelah shift kasir ditutup.",
                'url'      => '/cash-reconciliation',
                'label'    => 'Buka Rekonsiliasi',
            ],

            // ───── CUSTOMER PORTAL ──────────────────────────────────────────
            'cara_customer_portal' => [
                'keywords' => ['customer portal', 'portal pelanggan', 'pelanggan login', 'member area', 'website pelanggan'],
                'answer'   => "<strong>Customer Portal:</strong><br>APMS menyediakan portal khusus untuk pelanggan yang dapat diakses secara online. Melalui portal ini, pelanggan dapat:<br>- Melihat riwayat transaksi mereka<br>- Mengecek poin loyalitas<br>- Melihat status pesanan grosir<br>- Mendownload invoice<br><br>Untuk mengakses, pelanggan perlu mendaftar melalui tautan yang diberikan oleh toko.",
                'url'      => '/customer-portal',
                'label'    => 'Buka Portal Pelanggan',
            ],

            // ───── TENTANG APMS ─────────────────────────────────────────────
            'tentang_apms' => [
                'keywords' => ['apms itu apa', 'apa itu apms', 'penjelasan apms', 'fitur apms', 'fungsi sistem', 'sistem ini untuk apa', 'tentang sistem', 'informasi sistem'],
                'answer'   => "<strong>Tentang APMS (Ashar Parfum Management System):</strong><br>APMS adalah sistem manajemen toko parfum Enterprise yang dirancang khusus untuk operasional <strong>Ashar Parfum</strong>. Sistem ini mencakup seluruh aspek operasional bisnis parfum dalam satu platform terintegrasi.<br><br><strong>Fitur Utama:</strong><br>- <strong>POS / Kasir Eceran:</strong> Antarmuka kasir yang cepat dan intuitif dengan dukungan scan barcode<br>- <strong>Manajemen Grosir (Wholesale):</strong> Pesanan partai, invoice, dan pengiriman<br>- <strong>Inventory &amp; Audit Stok:</strong> Manajemen stok multi-gudang dengan stock opname<br>- <strong>Laporan &amp; Analitik:</strong> Grafik interaktif, laba rugi, dan analisis tren<br>- <strong>Karyawan &amp; Absensi:</strong> Manajemen pengguna dengan hak akses berbasis role<br>- <strong>Kupon &amp; Loyalty Program:</strong> Program loyalitas pelanggan berbasis poin<br>- <strong>Kas Bon / Piutang:</strong> Pencatatan piutang dengan pembayaran angsuran<br>- <strong>Multi-Cabang:</strong> Dukungan operasional untuk beberapa outlet<br>- <strong>Backup &amp; Restore:</strong> Keamanan data dengan backup rutin<br>- <strong>AI Copilot:</strong> Asisten digital yang sedang merespons Anda saat ini",
                'url'      => '/dashboard',
                'label'    => 'Ke Dashboard Utama',
            ],
        ];
    }
}
