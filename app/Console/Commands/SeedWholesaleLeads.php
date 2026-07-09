<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedWholesaleLeads extends Command
{
    protected $signature = 'leads:seed-wholesale';
    protected $description = 'Seed 60+ wholesale customer leads from competitor data';

    public function handle()
    {
        $leads = $this->getLeads();

        $branchMap = $this->buildBranchMap();

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($leads as $lead) {
            if (empty($lead['phone'])) {
                $this->warn("Skipped {$lead['name']}: no phone");
                $skipped++;
                continue;
            }

            $phone = $this->normalizePhone($lead['phone']);

            if (User::where('phone', $phone)->exists()) {
                $this->warn("Skipped {$lead['name']}: phone {$phone} already exists");
                $skipped++;
                continue;
            }

            $branchId = $this->resolveBranch($lead, $branchMap);

            $email = $this->generateEmail($lead['name']);

            if (User::where('email', $email)->exists()) {
                $email = $lead['name'] . '_' . Str::random(4) . '@apms-customer.com';
                $email = Str::slug($email) . '@apms-customer.com';
                if (User::where('email', $email)->exists()) {
                    $email = 'lead_' . Str::random(8) . '@apms-customer.com';
                }
            }

            try {
                $user = User::create([
                    'name' => $lead['name'],
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make('perfume2026'),
                    'role' => 'wholesale_customer',
                    'branch_id' => $branchId,
                    'can_login' => true,
                    'is_active' => true,
                    'referral_code' => strtoupper(Str::random(8)),
                ]);

                $existingCustomer = Customer::where('phone', $phone)->first();
                if (!$existingCustomer) {
                    Customer::create([
                        'customer_code' => 'CUST-' . strtoupper(Str::random(8)),
                        'name' => $lead['name'],
                        'phone' => $phone,
                        'type' => 'wholesale',
                        'is_active' => true,
                        'branch_id' => $branchId,
                        'address' => $lead['address'] ?? null,
                    ]);
                }

                $branchName = $branchId ? (Branch::find($branchId)?->name ?? 'N/A') : 'None';
                $this->info("Created: {$lead['name']} -> {$branchName}");
                $created++;
            } catch (\Exception $e) {
                $this->error("Failed {$lead['name']}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->table(
            ['Total', 'Created', 'Skipped', 'Errors'],
            [[count($leads), $created, $skipped, $errors]]
        );
    }

    private function resolveBranch(array $lead, array $branchMap): ?int
    {
        $address = strtolower($lead['address'] ?? '');
        $name = strtolower($lead['name'] ?? '');

        foreach ($branchMap as $keyword => $branchId) {
            if (str_contains($address, $keyword) || str_contains($name, $keyword)) {
                return $branchId;
            }
        }

        return $branchMap['cikarang'] ?? 13;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10) {
            return $phone;
        }

        if (substr($phone, 0, 2) === '62') {
            return '0' . substr($phone, 2);
        }

        if ($phone[0] !== '0') {
            return '0' . $phone;
        }

        return $phone;
    }

    private function generateEmail(string $name): string
    {
        $slug = Str::slug($name);
        $slug = substr($slug, 0, 40);
        return $slug . '@apms-customer.com';
    }

    private function buildBranchMap(): array
    {
        return [
            'lemahabang' => 22,
            'cibarusak' => 13,
            'cibeber' => 13,
            'cikarang baru' => 21,
            'cikarang' => 13,
            'jababeka' => 21,
            'pilar' => 18,
            'cibitung' => 19,
            'tambun' => 18,
            'setu' => 20,
            'bekasi' => 1,
            'mustika jaya' => 8,
            'jatiasih' => 6,
            'mangun jaya' => 69,
            'kedung waringin' => 23,
            'bojongmangu' => 24,
            'bantargebang' => 11,
            'rawalumbu' => 10,
            'pondok gede' => 9,
            'medan satria' => 12,
            'jatisampurna' => 7,
            'jakarta' => 25,
            'depok' => 30,
            'tangerang' => 31,
            'tangsel' => 32,
            'bogor' => 33,
            'karawang' => 71,
            'bandung' => 72,
            'cileungsi' => 20,
            'gunung putri' => 20,
            'cibinong' => 33,
        ];
    }

    private function getLeads(): array
    {
        return [
            ['name' => 'My Parfume 1', 'phone' => '083891521731', 'address' => 'Jl. Raya Lemahabang, RT.001/RW.006, Cikarang'],
            ['name' => 'LANERESA PARFUM', 'phone' => '', 'address' => 'Toko Pakaian (kategori tidak sesuai)'],
            ['name' => 'Flower Parfum', 'phone' => '089648463775', 'address' => 'Jl. Cibarusak Cikarang No.4, RW.8'],
            ['name' => 'Yansen Farfume Cikarang', 'phone' => '081211865947', 'address' => 'Jl. Kp. Dusun Cibeber No.80, RT.2/RW.6'],
            ['name' => 'Nila Sari Parfume 2', 'phone' => '', 'address' => 'Jl. Pangeran Jayakarta, Kp. Pulo Kapuk, RT.003/RW.05'],
            ['name' => 'My Parfume 2', 'phone' => '', 'address' => 'Jl. Kedasih I No.39'],
            ['name' => 'Al Muslimu Parfum', 'phone' => '081212808282', 'address' => 'Jl. Kedasih Raya, Cikarang Baru Raya'],
            ['name' => 'Toko Parfum Aromania', 'phone' => '081289270209', 'address' => 'P5F2+3M9, Jl. Raya Industri'],
            ['name' => 'Sampono Perfumery', 'phone' => '081323218880', 'address' => 'Jl. Raya Industri Sempu Darussalam No.23, RT.02/RW.08'],
            ['name' => 'OK Parfum Cikarang', 'phone' => '082124403100', 'address' => 'Unnamed Road'],
            ['name' => 'Uchi Parfume CKR Perum Graha Asri', 'phone' => '', 'address' => 'Graha Asri, Jl. Citarik Raya No.A 3'],
            ['name' => 'Queen Parfum', 'phone' => '', 'address' => 'P545+82R, Jl. Kp. Cibeureum'],
            ['name' => 'ISKAI PARFUME', 'phone' => '081261133707', 'address' => 'Jl. Raya Industri No.14, RT.003/06'],
            ['name' => 'Arigo Parfume', 'phone' => '081289856899', 'address' => 'Jl. Raya Industri, RT.004/RW.'],
            ['name' => 'Mulegh Parfum Jababeka', 'phone' => '085183073494', 'address' => 'Jl. Raya Industri, Jababeka Raya'],
            ['name' => 'LANERESA PARFUM Gardenville', 'phone' => '', 'address' => 'Ruko New GardenVille, Blok B21'],
            ['name' => 'Azzahra parfume', 'phone' => '085772275339', 'address' => 'Jl. Untung Suropati No.4A'],
            ['name' => 'Uchi Parfume Jababeka', 'phone' => '085156648489', 'address' => 'Jl. Raya Industri No.9'],
            ['name' => 'Sampono Parfumery', 'phone' => '085278200606', 'address' => 'Jl. Raya Industri, Jababeka Raya No.125'],
            ['name' => 'Laneresa Parfum 1', 'phone' => '', 'address' => 'Jl. Pangeran Jayakarta'],
            ['name' => 'djava parfum', 'phone' => '081388575408', 'address' => 'Jl. Sempu Kramat No.Ds, RT.04/RW.04'],
            ['name' => 'DHIKA REFILL PERFUME CABANG PILAR', 'phone' => '081285246768', 'address' => 'Jl. KH. Fudholi No.8'],
            ['name' => 'HASANAH PARFUME', 'phone' => '085717065573', 'address' => 'Jl. Cikarang Baru Raya No.1'],
            ['name' => 'Hijrah Parfum Laundry', 'phone' => '081322110250', 'address' => 'gg haji Ali blok A, Mekarmukti'],
            ['name' => 'Lynx Parfum', 'phone' => '087892926111', 'address' => 'Pondok Hijau, Ruko Jl. H. Ondo Suhandi No.70'],
            ['name' => 'Andri Parfum 1', 'phone' => '081280715981', 'address' => 'Jl. Rm. Marzuki No.42'],
            ['name' => 'Setya Parfum', 'phone' => '08561981297', 'address' => 'P47W+Q54'],
            ['name' => 'Parfum Haramain', 'phone' => '08119999508', 'address' => 'Jl. Kedasih Raya No.12 Blok A 1'],
            ['name' => 'Nila Sari Parfume', 'phone' => '085692149337', 'address' => 'Jl. Raya Industri No.20, RT.01/RW.06'],
            ['name' => 'My Parfume 3', 'phone' => '081317661110', 'address' => 'Jl. Citarum VII No.C2, RW.10'],
            ['name' => 'My parfume 4', 'phone' => '', 'address' => 'P5CM+GRG, Jl. Cisanggiri IID'],
            ['name' => 'Mp. My Parfum', 'phone' => '', 'address' => 'Jl. H. Saadi, RT.3/RW.3'],
            ['name' => 'Uchi Parfum Bekasi 16', 'phone' => '083816532735', 'address' => 'Jl. Inspeksi Kali CBL'],
            ['name' => 'Toko BERKAH PARFUM II', 'phone' => '085794143057', 'address' => 'Jl. Walahir, RT.2/RW.3'],
            ['name' => 'Presiden RU Parfume and Food', 'phone' => '083185518100', 'address' => 'Jl. Harapan Baru No.RT 03/09'],
            ['name' => 'Andri Parfum 2', 'phone' => '', 'address' => 'Jl. Asrama Brigif, RT.3/RW1'],
            ['name' => 'Home of Parfume Cikarang', 'phone' => '', 'address' => 'Unnamed Road'],
            ['name' => 'Dhika parfum 1', 'phone' => '081318717416', 'address' => 'Jl. Telaga Harapan Raya No.e9/9d'],
            ['name' => 'Dewi Parfum', 'phone' => '087741587010', 'address' => 'SGC Lantai G, Jl. RE. Martadinata No.61'],
            ['name' => 'BABA PARFUM CIKARANG', 'phone' => '', 'address' => 'Jl. Kp. Pasir Kunci, RT.13/RW.05'],
            ['name' => 'Nila Sari Parfum', 'phone' => '085779932255', 'address' => 'Jl. Asrama Brigif Desa No.118, RW.002/001'],
            ['name' => 'Toko Parfum Refill BERKAH PARFUM III', 'phone' => '', 'address' => 'Jl. Raya Lemahabang No.82'],
            ['name' => 'Dhika Parfume Jagawana', 'phone' => '081389618398', 'address' => 'Q554+W8J, Jl. Raya Jagawana, RT.04/RW.05'],
            ['name' => 'Sakha Parfum Pilar', 'phone' => '081297331603', 'address' => 'Pilar, Jl. Ki Hajar Dewantara No.14, RT.004/RW.005'],
            ['name' => 'Sayyida Cikarang', 'phone' => '08567035035', 'address' => 'Perum Puri Cikarang Mas No.12A, RT.004/RW.002'],
            ['name' => 'MOLAZ PERFUME', 'phone' => '', 'address' => 'Pertigaan Warung Bongkok'],
            ['name' => 'Laneresa Parfum 2', 'phone' => '082260365175', 'address' => 'P43W+CWR, Jl. Raya Industri, Jababeka Raya'],
            ['name' => 'parfum ZF cikarang', 'phone' => '089669768748', 'address' => 'Jalan Villa Mutiara Cikarang 1, Blk. H 18 No.19'],
            ['name' => 'Abu Salman parfum', 'phone' => '', 'address' => 'Kampung Cabang RT No.01/07'],
            ['name' => 'Sari Wangi Parfum', 'phone' => '', 'address' => 'Jl. Ust. Tata Sukarta'],
            ['name' => 'NAYLA PARFUM', 'phone' => '', 'address' => 'Jl. Cisangkuy II No.9 Blk A20'],
            ['name' => 'DHIKA PARFUME 2', 'phone' => '081315042607', 'address' => 'Jl. KH. Fudholi, RT.003/RW.04'],
            ['name' => 'Parfum Laundry Rajawangi Cikarang', 'phone' => '08132088988', 'address' => 'Jl. Citarik Raya Blk. A No.33'],
            ['name' => 'Baba Parfum Cibitung', 'phone' => '089505891787', 'address' => 'Perum Permata Nusa Indah Cibitung Blok E25 No.23'],
            ['name' => 'BERKAH PARFUM & DISTRIBUTOR NASA', 'phone' => '085794143057', 'address' => 'P4PQ+VWM, Jl. Keramik, RT.001/RW.02'],
            ['name' => 'My Parfum 5', 'phone' => '085771096719', 'address' => 'M5R9+GM6, Jl. Rusa Raya'],
            ['name' => 'El Parfum / El Pom Mini', 'phone' => '', 'address' => 'P543+597, Jl. Mandor Enggung'],
            ['name' => 'Abizhar parfum', 'phone' => '089507044130', 'address' => 'Jl. Raya Sukamantri, RT.006/RW.002, Karangbahagia'],
            ['name' => 'PT SOZIO DESCOLLONGES INDONESIA', 'phone' => '', 'address' => 'Kawasan Industri Jababeka, Jl. Jababeka V F Kav 5B'],
            ['name' => 'Supplier Baba Parfum Cikarang', 'phone' => '082175933233', 'address' => 'Jl. Taman Tropika No.31 Blk B2'],
            ['name' => 'Ni Wangi Parfum', 'phone' => '', 'address' => 'Jl. H. Bonar No.12'],
            ['name' => 'RAN_PartnerBekasi', 'phone' => '089669333460', 'address' => 'Perumahan Kartika Wanasari, RT008/RW031 No.8 Blok D4'],
            ['name' => 'Drindu Parfume Official', 'phone' => '085894076767', 'address' => 'Jl. Kukun'],
            ['name' => 'Dhika Parfum Pelaukan', 'phone' => '', 'address' => 'Jl. Kp. Pelaukan No.71-46'],
            ['name' => 'Mp Refill Parfume', 'phone' => '', 'address' => 'M5JP+55J, Jl. Tegal Danas'],
            ['name' => 'Scentalicious Parfume', 'phone' => '085282583515', 'address' => 'Jl. Jarakosta, RT.05/RW.08'],
            ['name' => 'Sakha Parfum', 'phone' => '081297331603', 'address' => 'Jl. KH. Fudholi, RT.03/RW.06'],
            ['name' => 'HEXA PARFUM', 'phone' => '', 'address' => 'P5VM+F6R'],
            ['name' => 'Otentic Perfumery', 'phone' => '089697700051', 'address' => 'Jl. Kp. Pelaukan No.Rt/RW 03/01'],
            ['name' => 'Cosmo Parfum Laundry', 'phone' => '085883703980', 'address' => 'Perumahan Puri Nirwana Residences Blok IK No.32, RT.007/RW.009'],
            ['name' => 'Susi Parfume', 'phone' => '', 'address' => 'Jl. Raya Sukamahi, RT.007/RW004'],
            ['name' => 'SANTRI MBELINK Parfum 3', 'phone' => '085729447253', 'address' => 'Perumahan Griya Hasanah Kalijaya No.65 Blok A3, RT.6/RW.8'],
            ['name' => 'Kelex Parfume Refills', 'phone' => '081386838749', 'address' => 'Perumahan Bumi Kahuripan Indah, Jl. Formula IV RT/RW 003/010'],
            ['name' => 'Uchi Parfume 2', 'phone' => '082124088110', 'address' => 'Jl. Raya Setu No.RT 02/03'],
            ['name' => 'Esencia Parfum Season 3', 'phone' => '089517646644', 'address' => 'Ruko Bizhaus, Jl. Metland Cibitung'],
            ['name' => 'dhika parfum 3', 'phone' => '', 'address' => 'P5W3+G4J, Jl. Setia Budi'],
            ['name' => 'UCHI PARFUME BEKASI 23', 'phone' => '088290283652', 'address' => 'Jl. Mega Raya'],
            ['name' => 'Masade Parfum TimTeng', 'phone' => '089662066510', 'address' => 'Villa Pesona Rubby, Blk. A2 No.10'],
            ['name' => 'Kyla Parfum dan Agen BRILink', 'phone' => '081399339575', 'address' => 'Ps. Tegal Danas, Jl. Kalimalang Hegar Mukti Lt 1'],
            ['name' => 'Ivory & Grace Perfume Official', 'phone' => '0895372115913', 'address' => 'Perumahan Bekasi Timur Permai, Jl. Pandawa Blok B4 No.03'],
            ['name' => 'QUEEN PARFUM 2', 'phone' => '', 'address' => 'Toko Pakaian, data tidak lengkap'],
            ['name' => 'Jastip In Parfume Bandung', 'phone' => '081219353247', 'address' => 'Perumahan Jl. Telaga Harapan Raya No.34 Blk h4'],
            ['name' => 'Belalang Parfum Shop', 'phone' => '', 'address' => 'Jl. Orchid 7'],
            ['name' => 'Faranza Refill Parfum', 'phone' => '', 'address' => 'Q5M5+39R, Jl. Simpang Tiga Buniayu'],
            ['name' => 'Chacha Parfum Refill', 'phone' => '081314384264', 'address' => 'M5CR+55W'],
            ['name' => 'Habib Parfum Kertamukti', 'phone' => '087824332145', 'address' => 'Perum Kerta Graha, Jl. Barokah 1, Jl. Raya Pisang Batu No.08 Blok A1'],
            ['name' => 'Laneresa Parfum 3', 'phone' => '081382087569', 'address' => 'Jl. Raya Cikarang - Cibarusah No.56'],
            ['name' => 'Nakay Perfume', 'phone' => '082387071557', 'address' => 'Jl. Telaga Pesona No.1'],
            ['name' => 'City Parfum', 'phone' => '081295926114', 'address' => 'P6MP+4X5'],
            ['name' => 'Sayyida Parfum Tambun Bekasi', 'phone' => '085180869324', 'address' => 'Gg. Musolah Alhikmah RT3/4'],
            ['name' => 'Auliya Parfume Bekasi', 'phone' => '085782498108', 'address' => 'Kampung Rawakuda'],
            ['name' => 'MS Glow dan Parfum DSulthan Cikarang', 'phone' => '082243778351', 'address' => 'Perum Griya Hegar Asri, Blok A2A No.1'],
            ['name' => 'Medina Parfume', 'phone' => '085883718101', 'address' => 'P3X5+FJC, Jl. Raya Mangun Jaya'],
            ['name' => 'Parfum Flower', 'phone' => '082210812976', 'address' => 'Cikarang'],
            ['name' => 'SUGI Slow Perfumery', 'phone' => '081370682378', 'address' => 'Jl. Trias No.127'],
            ['name' => 'Roman Perfume', 'phone' => '08574644222', 'address' => 'Jalan Chairil Anwar'],
            ['name' => 'Distributor Resmi Baba Parfum Cibitung', 'phone' => '081321453137', 'address' => 'Perum Griya Gandasari Indah, Blok A6 No.17, RT.003/RW.001'],
            ['name' => 'Mansion Parfum Bekasi', 'phone' => '08139558558', 'address' => 'Mall BTC Lt Dasar / D02 / 07, RT.003/RW.021'],
            ['name' => 'Ni Wangi Parfum 2', 'phone' => '085694237650', 'address' => 'Tambun, Bekasi'],
        ];
    }
}
