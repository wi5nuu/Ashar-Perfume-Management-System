<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1-100', 'name' => 'ASET',                  'type' => 'asset',     'normal_balance' => 'debit',  'level' => 0],
            ['code' => '1-101', 'name' => 'Kas',                   'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100'],
            ['code' => '1-102', 'name' => 'Bank',                  'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100'],
            ['code' => '1-103', 'name' => 'Piutang',               'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100'],
            ['code' => '1-104', 'name' => 'Piutang Grosir',        'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100'],
            ['code' => '1-105', 'name' => 'Persediaan Barang',     'type' => 'asset',     'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '1-100'],
            ['code' => '2-100', 'name' => 'KEWAJIBAN',             'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 0],
            ['code' => '2-101', 'name' => 'Utang Usaha',           'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100'],
            ['code' => '2-102', 'name' => 'Utang Pajak',           'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100'],
            ['code' => '2-103', 'name' => 'Utang Gaji',            'type' => 'liability', 'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '2-100'],
            ['code' => '3-100', 'name' => 'EKUITAS',               'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 0],
            ['code' => '3-101', 'name' => 'Modal',                 'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '3-100'],
            ['code' => '3-102', 'name' => 'Laba Ditahan',          'type' => 'equity',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '3-100'],
            ['code' => '3-103', 'name' => 'Prive',                 'type' => 'equity',    'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '3-100'],
            ['code' => '4-100', 'name' => 'PENDAPATAN',            'type' => 'income',    'normal_balance' => 'kredit', 'level' => 0],
            ['code' => '4-101', 'name' => 'Pendapatan Penjualan',  'type' => 'income',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100'],
            ['code' => '4-102', 'name' => 'Pendapatan Grosir',     'type' => 'income',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100'],
            ['code' => '4-103', 'name' => 'Pendapatan Lain-lain',  'type' => 'income',    'normal_balance' => 'kredit', 'level' => 1, 'parent_code' => '4-100'],
            ['code' => '5-100', 'name' => 'BEBAN',                 'type' => 'expense',   'normal_balance' => 'debit',  'level' => 0],
            ['code' => '5-101', 'name' => 'Harga Pokok Penjualan', 'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-102', 'name' => 'Beban Gaji',            'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-103', 'name' => 'Beban Sewa',            'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-104', 'name' => 'Beban Listrik & Air',   'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-105', 'name' => 'Beban Transportasi',    'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-106', 'name' => 'Beban Pemasaran',       'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-107', 'name' => 'Beban Administrasi',    'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-108', 'name' => 'Beban Penyusutan',      'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
            ['code' => '5-109', 'name' => 'Beban Lain-lain',       'type' => 'expense',   'normal_balance' => 'debit',  'level' => 1, 'parent_code' => '5-100'],
        ];

        foreach ($accounts as $acc) {
            $parentId = null;
            if (!empty($acc['parent_code'])) {
                $parent = ChartOfAccount::where('code', $acc['parent_code'])->first();
                $parentId = $parent?->id;
            }
            unset($acc['parent_code']);
            $acc['parent_id'] = $parentId;
            ChartOfAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }
        $this->command->info('COA seeded: 27 accounts');
    }
}
