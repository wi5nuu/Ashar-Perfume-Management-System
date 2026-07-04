<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BranchAndEmployeeSeeder extends Seeder
{
    private array $branches = [];

    public function run(): void
    {
        $this->command->info('Creating 100 branches across Indonesia...');

        // ─── BRANCH DATA ───────────────────────────────────────────────
        // Branch 1: Pusat (Central)
        $branches = [
            [
                'name' => 'Ashar Grosir Parfum - Pusat',
                'code' => 'PST-01',
                'address' => 'Jl. KH. Agus Salim No.45, RT.007/RW.006, Bekasi Jaya',
                'city' => 'Kota Bekasi',
                'phone' => '021-88880001',
                'is_active' => true,
            ],
        ];

        // Branches 2-40: Bekasi & Jababeka area (39 branches)
        $bekasiAreas = [
            'Bekasi Timur', 'Bekasi Barat', 'Bekasi Selatan', 'Bekasi Utara',
            'Jatiasih', 'Jatisampurna', 'Mustika Jaya', 'Pondok Gede',
            'Rawalumbu', 'Bantargebang', 'Medan Satria',
        ];
        $jababeka = ['Cikarang Pusat', 'Cikarang Utara', 'Cikarang Selatan', 'Cikarang Timur', 'Cikarang Barat'];
        $otherBekasi = ['Tambun', 'Cibitung', 'Setu', 'Cikarang', 'Lemahabang', 'Kedung Waringin', 'Bojongmangu'];
        $allBekasi = array_merge($bekasiAreas, $jababeka, $otherBekasi);

        $bekasiStreets = [
            'Jl. Raya', 'Jl. Industri', 'Jl. Merdeka', 'Jl. Ahmad Yani',
            'Jl. Diponegoro', 'Jl. Sudirman', 'Jl. Gunung Putri',
            'Jl. Kalimalang', 'Jl. Raya Cikarang', 'Jl. Raya Jababeka',
            'Jl. Raya Tambun', 'Jl. Raya Setu', 'Jl. Raya Bojongmangu',
        ];

        $counter = 2;
        foreach ($allBekasi as $area) {
            if ($counter > 40) break;
            $branches[] = [
                'name' => "Ashar Grosir Parfum - $area",
                'code' => 'BKS-' . str_pad($counter - 1, 2, '0', STR_PAD_LEFT),
                'address' => $bekasiStreets[array_rand($bekasiStreets)] . ' No.' . rand(1, 200) . ', ' . $area,
                'city' => $area . ', Bekasi',
                'phone' => '021-8888' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ];
            $counter++;
        }

        // Branches 41-70: Greater Jakarta (30 branches)
        $jabodetabek = [
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Selatan', 'Jakarta Timur',
            'Depok', 'Tangerang', 'Tangerang Selatan', 'Bogor', 'Cilegon',
            'Serang', 'Pandeglang', 'Lebak', 'Rangkasbitung', 'Parung Panjang',
            'Ciputat', 'Pamulang', 'Serpong', 'BSD City', 'Alam Sutera',
            'Kelapa Gading', 'Sunter', 'Pluit', 'Grogol', 'Kebon Jeruk',
            'Ciledug', 'Karawaci', 'Balaraja', 'Curug', 'Cikupa',
        ];

        // Extra Bekasi-area branches to fill 100 total
        $extraBekasi = [];
        for ($i = 0; $i < 16; $i++) {
            $extraBekasi[] = 'Bekasi ' . ($i + 1);
        }
        $jakartaStreets = [
            'Jl. Raya', 'Jl. M.H. Thamrin', 'Jl. Sudirman', 'Jl. Gatot Subroto',
            'Jl. Rasuna Said', 'Jl. Fatmawati', 'Jl. Margonda Raya',
        ];
        foreach ($jabodetabek as $city) {
            if ($counter > 70) break;
            $branches[] = [
                'name' => "Ashar Grosir Parfum - $city",
                'code' => 'JKT-' . str_pad($counter - 40, 2, '0', STR_PAD_LEFT),
                'address' => $jakartaStreets[array_rand($jakartaStreets)] . ' No.' . rand(1, 200) . ', ' . $city,
                'city' => $city,
                'phone' => '021-8888' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ];
            $counter++;
        }

        // Extra Bekasi branches to reach 100
        foreach ($extraBekasi as $area) {
            if ($counter > 70) break;
            $branches[] = [
                'name' => "Ashar Grosir Parfum - $area",
                'code' => 'BKS-' . str_pad($counter - 1, 2, '0', STR_PAD_LEFT),
                'address' => 'Jl. Raya Perjuangan No.' . rand(1, 200) . ', ' . $area,
                'city' => $area . ', Bekasi',
                'phone' => '021-8888' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ];
            $counter++;
        }

        // Branches 71-100: Major cities across Indonesia (30 branches)
        $indonesiaCities = [
            'Bandung', 'Semarang', 'Yogyakarta', 'Surakarta', 'Surabaya',
            'Malang', 'Denpasar', 'Mataram', 'Kupang', 'Makassar',
            'Manado', 'Palu', 'Kendari', 'Gorontalo', 'Ambon',
            'Jayapura', 'Manokwari', 'Sorong', 'Medan', 'Padang',
            'Pekanbaru', 'Palembang', 'Bengkulu', 'Lampung', 'Pangkal Pinang',
            'Banjarmasin', 'Samarinda', 'Balikpapan', 'Pontianak', 'Palangka Raya', 'Ternate',
        ];
        $indonesiaStreets = [
            'Jl. Raya', 'Jl. Merdeka', 'Jl. Ahmad Yani', 'Jl. Diponegoro', 'Jl. Sudirman',
        ];
        foreach ($indonesiaCities as $city) {
            if ($counter > 101) break;
            $branches[] = [
                'name' => "Ashar Grosir Parfum - $city",
                'code' => 'REG-' . str_pad($counter - 70, 2, '0', STR_PAD_LEFT),
                'address' => $indonesiaStreets[array_rand($indonesiaStreets)] . ' No.' . rand(1, 200) . ', ' . $city,
                'city' => $city,
                'phone' => '021-8888' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ];
            $counter++;
        }

        // Insert branches
        foreach ($branches as $data) {
            Branch::firstOrCreate(['code' => $data['code']], $data);
        }
        $this->command->info('Created ' . count($branches) . ' branches.');

        $centralBranch = Branch::where('code', 'PST-01')->first();
        $otherBranches = Branch::where('code', '!=', 'PST-01')->get();

        // ─── USERS ────────────────────────────────────────────────────

        $createUser = function (string $name, string $email, string $role, ?int $branchId) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'full_name' => $name,
                    'email' => $email,
                    'password' => bcrypt(bin2hex(random_bytes(12))),
                    'role' => $role,
                    'branch_id' => $branchId,
                    'can_login' => true,
                    'is_active' => true,
                ]
            );
            $rbacRole = Role::where('slug', $role)->first();
            if ($rbacRole) {
                $user->roles()->sync([$rbacRole->id]);
            }
            return $user;
        };

        $this->command->info('Creating 1 Owner (pusat)...');
        $createUser('Owner Ashar', 'owner@asharparfum.com', 'owner', null);

        $this->command->info('Creating 1 Admin Pusat...');
        $adminPusat = User::firstOrCreate(
            ['email' => 'admin@asharparfum.com'],
            [
                'name' => 'Admin Pusat',
                'full_name' => 'Admin Pusat',
                'email' => 'admin@asharparfum.com',
                'password' => bcrypt(bin2hex(random_bytes(12))),
                'role' => 'admin',
                'branch_id' => null,
                'can_login' => true,
                'is_active' => true,
            ]
        );
        $rbacPusat = Role::where('slug', 'admin_pusat')->first();
        if ($rbacPusat) {
            $adminPusat->roles()->sync([$rbacPusat->id]);
        }

        $this->command->info('Creating 100 Admin Cabang (1 per branch)...');
        foreach ($otherBranches as $i => $branch) {
            $label = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $createUser(
                "Admin Cabang {$branch->city}",
                "cab.$label@asharparfum.com",
                'admin',
                $branch->id
            );
        }

        $this->command->info('Creating 1 Manager demo...');
        $createUser('Manager Demo', 'manager@asharparfum.com', 'manager', $otherBranches->first()->id);

        $this->command->info('Creating 1 Supervisor demo...');
        $createUser('Supervisor Demo', 'supervisor@asharparfum.com', 'supervisor', $otherBranches->first()->id);

        $this->command->info('Creating 1 Cashier demo...');
        $createUser('Cashier Demo', 'cashier@asharparfum.com', 'cashier', $otherBranches->first()->id);

        $this->command->info('Creating 1 Gudang demo...');
        $createUser('Gudang Demo', 'warehouse@asharparfum.com', 'warehouse', $centralBranch->id);

        $this->command->info('');
        $this->command->info('=== SEEDING COMPLETE ===');
        $this->command->info("Total branches: " . Branch::count());
        $this->command->info("Total users: " . User::count());
        $this->command->info('');
        $this->command->info('Login credentials (lihat credentials/default-login.json):');
        $this->command->info('  owner@asharparfum.com        — Owner (pusat)');
        $this->command->info('  admin@asharparfum.com        — Admin Pusat');
        $this->command->info('  cab.01@asharparfum.com       — Admin Cabang (branch 1)');
        $this->command->info('  cab.99@asharparfum.com       — Admin Cabang (branch 99)');
        $this->command->info('  manager@asharparfum.com      — Manager (demo)');
        $this->command->info('  supervisor@asharparfum.com   — Supervisor (demo)');
        $this->command->info('  cashier@asharparfum.com      — Cashier (demo)');
        $this->command->info('  warehouse@asharparfum.com    — Gudang (demo)');

        // ─── STORE EMPLOYEES: 2 per cabang (200 total) ────────────────
        $this->command->info('');
        $this->command->info('Creating 200 store employees (2 per cabang branch)...');

        $firstNames = ['Ahmad','Budi','Citra','Dewi','Eko','Fitri','Gilang','Hesti','Indra','Joko','Kurnia','Lina','Mega','Nadia','Oscar','Putri','Qori','Rina','Sigit','Tina','Ujang','Vina','Wawan','Yuli','Zainal','Agus','Bambang','Cahya','Dodi','Eka','Farhan','Gita','Hendra','Irma','Juni','Kartika','Luki','Maya','Niko','Olga','Paramita','Ratna','Sari','Teguh','Utami','Wahyu','Yanti','Andi','Bayu','Cici','Desi','Endah','Fajar','Galuh','Herman','Irwan','Jamilah','Karim','Laras','Mila','Nana','Opik','Puspita','Rahmat','Santi','Tika','Umar','Winda','Yuda','Adi','Bunga','Cici','Dimas','Elok','Fajar','Gendis','Hadi','Ida','Jali','Kiki','Laras','Maman','Nina','Oman','Puput','Rizal','Sari','Tomi','Ujang','Wira','Yana','Agung','Bella','Candra','Deni','Eli','Fifi','Gatot','Hani','Irfan','Jeni','Kunto','Linda','Miko','Nurul','Oki','Pipit','Rama','Sari','Tedi','Upik','Widya','Yoga'];
        $lastNames = ['Susanto','Wijaya','Kusuma','Pratama','Santoso','Gunawan','Saputra','Wibowo','Hidayat','Nugroho','Setiawan','Utomo','Siregar','Sihombing','Nainggolan','Simanjuntak','Situmorang','Pasaribu','Sinaga','Hasibuan','Harahap','Nasution','Lubis','Dalimunthe','Manurung','Siahaan','Saragih','Gultom','Situmeang','Rajagukguk','Sitompul','Tamba','Panggabean','Silalahi','Haloho','Sembiring','Tarigan','Ginting','Karo-Karo','Peranginangin','Sinuraya','Barus','Padang','Hutasoit','Situngkir','Sidabutar','Panjaitan','Simatupang','Silitonga','Sianturi','Samosir','Siallagan','Sidabutar','Butar-Butar','Sinulingga','Saragi','Sitorus','Tampubolon','Simamora','Hutapea','Sihotang','Sipayung','Manik','Sagala','Sibarani','Sitepu','Sidabalok','Sinuraya','Silitonga','Siahaan','Siregar','Sihombing','Nainggolan'];
        $religions = ['islam','islam','islam','islam','protestan','katolik','hindu','buddha'];
        $educations = ['SMA/SMK','SMA/SMK','SMA/SMK','SMA/SMK','D3','D3','S1','S1','S1'];
        $maritals = ['single','single','single','married','married','married'];
        $employStatuses = ['permanent','permanent','contract','contract','probation','probation'];
        $banks = ['BCA','BNI','BRI','Mandiri','BSI','BJB'];

        $createStoreEmployee = function (string $email, string $name, array $data, int $branchId) {
            $nik = $data['nik'];
            return User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'full_name' => $data['full_name'],
                    'nickname' => $data['nickname'],
                    'nik' => $nik,
                    'gender' => $data['gender'],
                    'place_of_birth' => $data['place_of_birth'],
                    'date_of_birth' => $data['date_of_birth'],
                    'religion' => $data['religion'],
                    'marital_status' => $data['marital_status'],
                    'last_education' => $data['last_education'],
                    'join_date' => $data['join_date'],
                    'employee_id' => $data['employee_id'],
                    'employment_status' => $data['employment_status'],
                    'bank_name' => $data['bank_name'],
                    'bank_account_number' => $data['bank_account_number'],
                    'bank_account_holder' => $data['bank_account_holder'],
                    'npwp' => $data['npwp'],
                    'basic_salary' => $data['basic_salary'],
                    'phone' => $data['phone'],
                    'email' => $email,
                    'password' => bcrypt(bin2hex(random_bytes(12))),
                    'role' => 'employee',
                    'branch_id' => $branchId,
                    'can_login' => false,
                    'is_active' => true,
                    'emergency_contact_name' => $data['emergency_contact_name'],
                    'emergency_contact_phone' => $data['emergency_contact_phone'],
                    'emergency_contact_relation' => $data['emergency_contact_relation'],
                ]
            );
        };

        $now = Carbon::now();
        $count = 0;
        foreach ($otherBranches as $branch) {
            for ($n = 1; $n <= 2; $n++) {
                $label = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
                $gender = rand(0, 1) ? 'male' : 'female';
                $fn = $firstNames[array_rand($firstNames)];
                $ln = $lastNames[array_rand($lastNames)];
                $fullName = $fn . ' ' . $ln;
                $nick = $fn;
                $birthDate = Carbon::createFromDate(rand(1975, 2000), rand(1, 12), rand(1, 28));
                $joinDate = Carbon::createFromDate(rand(2020, 2025), rand(1, 12), rand(1, 28));
                $nik = rand(1000000000000000, 9999999999999999); // 16 digits
                $phone = '08' . rand(1000000000, 9999999999);

                $data = [
                    'nik' => $nik,
                    'full_name' => $fullName,
                    'nickname' => $nick,
                    'gender' => $gender,
                    'place_of_birth' => $branch->city,
                    'date_of_birth' => $birthDate,
                    'religion' => $religions[array_rand($religions)],
                    'marital_status' => $maritals[array_rand($maritals)],
                    'last_education' => $educations[array_rand($educations)],
                    'join_date' => $joinDate,
                    'employee_id' => 'AGP-' . $branch->code . '-' . str_pad($n, 2, '0', STR_PAD_LEFT),
                    'employment_status' => $employStatuses[array_rand($employStatuses)],
                    'bank_name' => $banks[array_rand($banks)],
                    'bank_account_number' => (string)rand(1000000000, 9999999999),
                    'bank_account_holder' => $fullName,
                    'npwp' => sprintf('%02d.%03d.%03d.%03d', rand(1,99), rand(1,999), rand(1,999), rand(1,999)),
                    'basic_salary' => rand(3000000, 6000000),
                    'phone' => $phone,
                    'emergency_contact_name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'emergency_contact_phone' => '08' . rand(1000000000, 9999999999),
                    'emergency_contact_relation' => ['Orang Tua','Suami/Istri','Saudara','Teman'][array_rand(['Orang Tua','Suami/Istri','Saudara','Teman'])],
                ];

                $email = 'emp.' . str_pad($branch->code, 8, '0', STR_PAD_LEFT) . '.' . $n . '@asharparfum.com';
                $createStoreEmployee($email, $nick, $data, $branch->id);
                $count++;
            }
        }

        $this->command->info("Created $count store employees.");
        $this->command->info('');
        $this->command->info('Store employee email pattern: emp.{branch_code}.{n}@asharparfum.com');
        $this->command->info('Example: emp.BKS-01.1@asharparfum.com (Cabang 1, employee 1)');
        $this->command->info('Passwords: lihat credentials/default-login.json');
        $this->command->info('NOTE: These accounts have can_login=false (attendance only).');
    }
}
