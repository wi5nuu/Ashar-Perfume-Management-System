<?php

return [

    'reserved_words' => [
        'masuk', 'keluar', 'laris', 'terlaris', 'habis', 'kritis', 'stok',
        'penjualan', 'laba', 'rugi', 'untung', 'pelanggan', 'cabang',
        'karyawan', 'shift', 'kasir', 'promo', 'kupon', 'grosir', 'pesanan',
        'cabang', 'outlet', 'toko', 'belanja', 'modal', 'biaya', 'pengeluaran',
        'bonus', 'diskon', 'hutang', 'piutang', 'retur', 'supplier', 'pegawai',
        'restock', 'kedatangan', 'barang', 'customer', 'member','domisili','kota',
        'pegawai','staff','direktur','owner','admin','manager','supervisor',
        'hadir','izin','sakit','alpha','terlambat','pulang','datang',
        'kadaluarsa','expired','kadaluwarsa','kedaluwarsa','batch','kode',
        'vendor','pemasok','tenggat','jatuh tempo','utang','tagihan',
    ],

    'intents' => [

        'best_selling_products' => [
            'keywords' => ['laris', 'best seller', 'paling laku', 'top jual', 'terjual'],
            'phrases'  => ['parfum terlaris', 'produk terlaris', 'paling laris', 'paling laku', 'best seller', 'top selling'],
            'handler'  => \App\Services\CopilotIntents\BestSellingHandler::class,
        ],

        'incoming_stock' => [
            'keywords' => ['masuk', 'restock', 'kedatangan', 'datang', 'dikirim'],
            'phrases'  => ['barang masuk', 'stok masuk', 'akan masuk', 'purchase order', 'po masuk'],
            'handler'  => \App\Services\CopilotIntents\IncomingStockHandler::class,
        ],

        'branch_info' => [
            'keywords' => ['cabang', 'outlet'],
            'phrases'  => ['berapa cabang', 'jumlah cabang', 'daftar cabang', 'info cabang', 'semua cabang'],
            'handler'  => \App\Services\CopilotIntents\BranchInfoHandler::class,
        ],

        'customer_count' => [
            'keywords' => ['pelanggan', 'customer', 'member'],
            'phrases'  => ['jumlah pelanggan', 'berapa pelanggan', 'total pelanggan', 'banyak pelanggan'],
            'handler'  => \App\Services\CopilotIntents\CustomerCountHandler::class,
        ],

        'customer_origin' => [
            'keywords' => ['asal', 'domisili', 'alamat', 'sebaran'],
            'phrases'  => ['darimana pelanggan', 'asal pelanggan', 'sebaran pelanggan', 'pelanggan dari mana', 'daerah pelanggan'],
            'handler'  => \App\Services\CopilotIntents\CustomerOriginHandler::class,
        ],

        'stock_summary' => [
            'keywords' => ['stok', 'inventaris', 'inventory', 'persediaan', 'barang'],
            'phrases'  => ['stok barang', 'ketersediaan stok', 'semua stok', 'total stok'],
            'handler'  => \App\Services\CopilotIntents\StockSummaryHandler::class,
        ],

        'critical_stock' => [
            'keywords' => ['habis', 'kritis', 'minim', 'kurang', 'kosong', 'abis', 'tidak ada', 'menipis', 'nyaris'],
            'phrases'  => ['stok habis', 'stok kritis', 'stok kurang', 'out of stock', 'barang habis', 'stok menipis', 'stok kosong', 'produk habis'],
            'handler'  => \App\Services\CopilotIntents\CriticalStockHandler::class,
        ],

        'sales_summary' => [
            'keywords' => ['penjualan', 'transaksi', 'omzet', 'pendapatan', 'jual', 'terjual'],
            'phrases'  => ['penjualan hari ini', 'total penjualan', 'laporan penjualan', 'omzet hari ini', 'berapa penjualan'],
            'handler'  => \App\Services\CopilotIntents\SalesSummaryHandler::class,
        ],

        'profit_loss' => [
            'keywords' => ['laba', 'rugi', 'untung', 'profit', 'keuntungan', 'pendapatan bersih'],
            'phrases'  => ['laba rugi', 'keuntungan hari ini', 'profit hari ini', 'laba bersih', 'rugi laba'],
            'handler'  => \App\Services\CopilotIntents\ProfitLossHandler::class,
        ],

        'expense_summary' => [
            'keywords' => ['biaya', 'pengeluaran', 'expense', 'modal', 'belanja', 'keluar'],
            'phrases'  => ['pengeluaran hari ini', 'biaya hari ini', 'total pengeluaran', 'laporan biaya'],
            'handler'  => \App\Services\CopilotIntents\ExpenseHandler::class,
        ],

        'employee_info' => [
            'keywords' => ['karyawan', 'pegawai', 'staff', 'pegawai'],
            'phrases'  => ['jumlah karyawan', 'data karyawan', 'info karyawan', 'daftar karyawan', 'berapa karyawan'],
            'handler'  => \App\Services\CopilotIntents\EmployeeInfoHandler::class,
        ],

        'shift_status' => [
            'keywords' => ['shift', 'kasir'],
            'phrases'  => ['status shift', 'shift hari ini', 'laporan shift', 'siapa bertugas'],
            'handler'  => \App\Services\CopilotIntents\ShiftStatusHandler::class,
        ],

        'active_promos' => [
            'keywords' => ['promo', 'kupon', 'diskon', 'promosi', 'voucher'],
            'phrases'  => ['promo aktif', 'kupon aktif', 'promo hari ini', 'diskon berlaku', 'voucher tersedia'],
            'handler'  => \App\Services\CopilotIntents\ActivePromosHandler::class,
        ],

        'wholesale_status' => [
            'keywords' => ['grosir', 'wholesale', 'pesanan', 'pesanan grosir'],
            'phrases'  => ['pesanan grosir', 'status grosir', 'pesanan pending', 'order grosir'],
            'handler'  => \App\Services\CopilotIntents\WholesaleOrderHandler::class,
        ],

        'daily_recap' => [
            'keywords' => ['rekap', 'rangkuman', 'ringkasan', 'hari ini', 'report', 'laporan'],
            'phrases'  => ['rekap hari ini', 'rangkuman harian', 'laporan harian', 'daily report', 'recap today'],
            'handler'  => \App\Services\CopilotIntents\DailyRecapHandler::class,
        ],

        'attendance_info' => [
            'keywords' => ['hadir', 'absensi', 'kehadiran', 'masuk kerja', 'datang'],
            'phrases'  => ['absensi hari ini', 'kehadiran hari ini', 'siapa hadir', 'daftar hadir'],
            'handler'  => \App\Services\CopilotIntents\AttendanceHandler::class,
        ],

    ],

];
