<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ═══════════════════════════════════════════════════════════════
            // 1. ASET
            // ═══════════════════════════════════════════════════════════════
            ['code' => '1-100', 'name' => 'ASET',                    'type' => 'asset',     'normal_balance' => 'debit',  'level' => 0, 'is_posting' => false, 'description' => 'Header aset'],
            ['code' => '1-101', 'name' => 'Kas',                     'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'is_cash' => true, 'description' => 'Kas di tangan (cash register & shift)'],
            ['code' => '1-102', 'name' => 'Bank',                    'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'is_bank' => true, 'description' => 'Rekening bank operasional'],
            ['code' => '1-103', 'name' => 'Piutang Usaha (Retail)',  'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'description' => 'Piutang kas bon pelanggan eceran'],
            ['code' => '1-104', 'name' => 'Piutang Usaha (Grosir)',  'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'description' => 'Piutang order grosir / B2B'],
            ['code' => '1-105', 'name' => 'Persediaan Barang Dagang', 'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'description' => 'Persediaan produk jadi & botol siap jual'],
            ['code' => '1-106', 'name' => 'Persediaan Bahan Baku (Bibit & Kemasan)', 'type' => 'asset', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '1-100', 'description' => 'Bibit parfum, botol, dan kemasan'],
            ['code' => '1-107', 'name' => 'Perlengkapan Toko',       'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'description' => 'Perlengkapan habis pakai'],
            ['code' => '1-110', 'name' => 'ASET TETAP',              'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100', 'is_posting' => false, 'description' => 'Header aset tetap'],
            ['code' => '1-111', 'name' => 'Peralatan Toko & Display', 'type' => 'asset',     'normal_balance' => 'debit',  'level' => 2, 'parent_code' => '1-110', 'description' => 'Etalase, rak display, mesin kasir'],
            ['code' => '1-112', 'name' => 'Kendaraan Operasional',   'type' => 'asset',     'normal_balance' => 'debit',  'level' => 2, 'parent_code' => '1-110', 'description' => 'Kendaraan untuk distribusi grosir'],
            ['code' => '1-113', 'name' => 'Peralatan Komputer',      'type' => 'asset',     'normal_balance' => 'debit',  'level' => 2, 'parent_code' => '1-110', 'description' => 'Komputer, printer, perangkat IT'],
            ['code' => '1-114', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'asset', 'normal_balance' => 'kredit', 'level' => 2, 'parent_code' => '1-110', 'description' => 'Kontra-aset penyusutan peralatan'],
            ['code' => '1-115', 'name' => 'Akumulasi Penyusutan Kendaraan', 'type' => 'asset', 'normal_balance' => 'kredit', 'level' => 2, 'parent_code' => '1-110', 'description' => 'Kontra-aset penyusutan kendaraan'],

            // ═══════════════════════════════════════════════════════════════
            // 2. KEWAJIBAN
            // ═══════════════════════════════════════════════════════════════
            ['code' => '2-100', 'name' => 'KEWAJIBAN',               'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 0, 'is_posting' => false, 'description' => 'Header kewajiban'],
            ['code' => '2-101', 'name' => 'Utang Usaha',             'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'Utang ke supplier atas pembelian'],
            ['code' => '2-102', 'name' => 'Utang Pajak (PPN Keluaran)', 'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'PPN keluaran yang belum disetor'],
            ['code' => '2-103', 'name' => 'Utang Gaji',              'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'Gaji terutang akhir bulan'],
            ['code' => '2-104', 'name' => 'Utang BPJS',              'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'Iuran BPJS Kesehatan & Ketenagakerjaan'],
            ['code' => '2-105', 'name' => 'Utang PPh 21',            'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'PPh 21 karyawan yang belum disetor'],
            ['code' => '2-106', 'name' => 'Utang THR & Bonus',       'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'Akrual THR / bonus karyawan'],
            ['code' => '2-107', 'name' => 'Pendapatan Diterima Dimuka', 'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100', 'description' => 'DP / pembayaran di muka pelanggan'],

            // ═══════════════════════════════════════════════════════════════
            // 3. EKUITAS
            // ═══════════════════════════════════════════════════════════════
            ['code' => '3-100', 'name' => 'EKUITAS',                 'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 0, 'is_posting' => false, 'description' => 'Header ekuitas'],
            ['code' => '3-101', 'name' => 'Modal Pemilik',           'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '3-100', 'description' => 'Modal disetor pemilik'],
            ['code' => '3-102', 'name' => 'Laba Ditahan',            'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '3-100', 'description' => 'Akumulasi laba periode lalu'],
            ['code' => '3-103', 'name' => 'Prive / Penarikan Pemilik', 'type' => 'equity',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '3-100', 'description' => 'Penarikan kas oleh pemilik'],

            // ═══════════════════════════════════════════════════════════════
            // 4. PENDAPATAN
            // ═══════════════════════════════════════════════════════════════
            ['code' => '4-100', 'name' => 'PENDAPATAN',              'type' => 'income',    'normal_balance' => 'kredit', 'level' => 0, 'is_posting' => false, 'description' => 'Header pendapatan'],
            ['code' => '4-101', 'name' => 'Pendapatan Penjualan Eceran', 'type' => 'income', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100', 'description' => 'Penjualan POS eceran'],
            ['code' => '4-102', 'name' => 'Pendapatan Penjualan Grosir', 'type' => 'income', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100', 'description' => 'Penjualan B2B wholesale'],
            ['code' => '4-103', 'name' => 'Pendapatan Jasa Isi Ulang (Refill)', 'type' => 'income', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100', 'description' => 'Jasa refill parfum per ml'],
            ['code' => '4-104', 'name' => 'Pendapatan Ongkos Kirim', 'type' => 'income',   'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100', 'description' => 'Ongkir ditagihkan ke pelanggan'],
            ['code' => '4-105', 'name' => 'Pendapatan Lain-lain',   'type' => 'income',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100', 'description' => 'Pendapatan non-operasional'],

            // ═══════════════════════════════════════════════════════════════
            // 5. BEBAN
            // ═══════════════════════════════════════════════════════════════
            ['code' => '5-100', 'name' => 'BEBAN',                   'type' => 'expense',   'normal_balance' => 'debit',  'level' => 0, 'is_posting' => false, 'description' => 'Header beban'],
            ['code' => '5-101', 'name' => 'Harga Pokok Penjualan',  'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'COGS barang terjual'],
            ['code' => '5-102', 'name' => 'Beban Gaji & Upah',      'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Gaji karyawan (pokok + tunjangan + komisi)'],
            ['code' => '5-103', 'name' => 'Beban Sewa',             'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Sewa toko & gudang'],
            ['code' => '5-104', 'name' => 'Beban Listrik & Air',    'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Utilitas toko'],
            ['code' => '5-105', 'name' => 'Beban Transportasi & Distribusi', 'type' => 'expense', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '5-100', 'description' => 'Pengiriman, BBM, kurir'],
            ['code' => '5-106', 'name' => 'Beban Pemasaran & Promosi', 'type' => 'expense', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '5-100', 'description' => 'Iklan, tester gratis, promosi'],
            ['code' => '5-107', 'name' => 'Beban Administrasi & Umum', 'type' => 'expense', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '5-100', 'description' => 'ATK, langganan software, perizinan'],
            ['code' => '5-108', 'name' => 'Beban Penyusutan',       'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Penyusutan aset tetap'],
            ['code' => '5-109', 'name' => 'Beban Perlengkapan Toko', 'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Plastik, label, perlengkapan habis pakai'],
            ['code' => '5-110', 'name' => 'Beban Telepon & Internet', 'type' => 'expense', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '5-100', 'description' => 'Komunikasi kantor'],
            ['code' => '5-111', 'name' => 'Beban BPJS & Jamsostek', 'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Iuran BPJS ditanggung perusahaan'],
            ['code' => '5-112', 'name' => 'Beban Perawatan & Perbaikan', 'type' => 'expense', 'normal_balance' => 'debit', 'level' => 1, 'parent_code' => '5-100', 'description' => 'Maintenance toko & peralatan'],
            ['code' => '5-113', 'name' => 'Beban Lain-lain',        'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100', 'description' => 'Beban operasional lainnya'],
        ];

        foreach ($accounts as $acc) {
            $parentId = null;
            if (! empty($acc['parent_code'])) {
                $parent = ChartOfAccount::where('code', $acc['parent_code'])->first();
                $parentId = $parent?->id;
            }
            unset($acc['parent_code']);
            $acc['parent_id'] = $parentId;
            ChartOfAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }

        // Pastikan flag posting/cash/bank benar di instalasi yang sudah ada.
        $this->applyFlags();
        if ($this->command) {
            $this->command->info('COA seeded: '.ChartOfAccount::count().' accounts');
        }
    }

    /**
     * Update flags for existing installations (idempotent).
     */
    private function applyFlags(): void
    {
        ChartOfAccount::whereIn('code', ['1-101', '1-102'])->update(['is_cash' => false]);
        ChartOfAccount::where('code', '1-101')->update(['is_cash' => true]);
        ChartOfAccount::where('code', '1-102')->update(['is_bank' => true]);
        ChartOfAccount::where('level', 0)->update(['is_posting' => false]);
    }
}
