<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // Create the default "Pusat" branch
        $pusat = Branch::create([
            'name'         => 'Ashar Parfum - Pusat',
            'code'         => 'CAB-00',
            'address'      => 'Alamat Toko Pusat',
            'city'         => 'Kota Pusat',
            'phone'        => '-',
            'manager_name' => 'Owner',
            'is_active'    => true,
            'notes'        => 'Cabang utama / toko pusat. Data lama sebelum fitur multi-cabang.',
        ]);

        // Assign all existing non-owner users to the default branch
        DB::table('users')
            ->where('role', '!=', 'owner')
            ->whereNull('branch_id')
            ->update(['branch_id' => $pusat->id]);

        // Also tag all old transactions and expenses with this branch
        DB::table('transactions')->whereNull('branch_id')->update(['branch_id' => $pusat->id]);
        DB::table('expenses')->whereNull('branch_id')->update(['branch_id' => $pusat->id]);
    }
}
