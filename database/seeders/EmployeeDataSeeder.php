<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Filling missing employee data for all users...');

        $firstNames = ['Ahmad','Budi','Citra','Dewi','Eko','Fitri','Gilang','Hesti','Indra','Joko','Kurnia','Lina','Mega','Nadia','Oscar','Putri','Rina','Sigit','Tina','Ujang','Vina','Wawan','Yuli','Zainal','Agus','Bambang','Cahya','Dodi','Eka','Farhan','Gita','Hendra','Irma','Juni','Kartika','Luki','Maya','Niko','Paramita','Ratna','Sari','Teguh','Utami','Wahyu','Yanti','Andi','Bayu','Cici','Desi','Endah','Fajar','Galuh','Herman','Irwan','Jamilah','Karim','Laras','Mila','Nana','Puspita','Rahmat','Santi','Tika','Umar','Winda','Yuda','Adi','Bunga','Dimas','Elok','Fajar','Gendis','Hadi','Ida','Jali','Kiki','Laras','Maman','Nina','Oman','Puput','Rizal','Tomi','Wira','Yana','Agung','Bella','Candra','Deni','Eli','Fifi','Gatot','Hani','Irfan','Jeni','Kunto','Linda','Miko','Nurul','Oki','Pipit','Rama','Sari','Tedi','Widya','Yoga'];
        $lastNames = ['Susanto','Wijaya','Kusuma','Pratama','Santoso','Gunawan','Saputra','Wibowo','Hidayat','Nugroho','Setiawan','Utomo','Siregar','Sihombing','Nainggolan','Simanjuntak','Situmorang','Pasaribu','Sinaga','Hasibuan','Harahap','Nasution','Lubis','Dalimunthe','Manurung','Siahaan','Saragih','Gultom','Situmeang','Rajagukguk','Sitompul','Tamba','Panggabean','Silalahi','Haloho','Sembiring','Tarigan','Ginting','Karo-Karo','Peranginangin','Sinuraya','Barus','Padang','Hutasoit','Situngkir','Sidabutar','Panjaitan','Simatupang','Silitonga','Sianturi','Samosir','Siallagan','Butar-Butar','Sinulingga','Saragi','Sitorus','Tampubolon','Simamora','Hutapea','Sihotang','Sipayung','Manik','Sagala','Sibarani','Sitepu','Sibuea','Sinurat','Simbolon'];
        $religions = ['islam','islam','islam','islam','protestan','katolik','hindu','buddha'];
        $educations = ['SMA/SMK','SMA/SMK','D3','D3','S1','S1','S1'];
        $maritals = ['single','single','single','married','married','married'];
        $employStatuses = ['permanent','permanent','contract','contract','probation','probation'];
        $banks = ['BCA','BNI','BRI','Mandiri','BSI','BJB','Mandiri Syariah'];

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $needsUpdate = false;
            $updateData = [];

            if (empty($user->nik)) {
                $updateData['nik'] = (string)rand(1000000000000000, 9999999999999999);
                $needsUpdate = true;
            }
            if (empty($user->gender)) {
                $updateData['gender'] = rand(0, 1) ? 'male' : 'female';
                $needsUpdate = true;
            }
            if (empty($user->place_of_birth)) {
                $updateData['place_of_birth'] = $user->branch?->city ?? 'Bekasi';
                $needsUpdate = true;
            }
            if (empty($user->date_of_birth)) {
                $updateData['date_of_birth'] = Carbon::createFromDate(rand(1975, 2000), rand(1, 12), rand(1, 28));
                $needsUpdate = true;
            }
            if (empty($user->religion)) {
                $updateData['religion'] = $religions[array_rand($religions)];
                $needsUpdate = true;
            }
            if (empty($user->marital_status)) {
                $updateData['marital_status'] = $maritals[array_rand($maritals)];
                $needsUpdate = true;
            }
            if (empty($user->last_education)) {
                $updateData['last_education'] = $educations[array_rand($educations)];
                $needsUpdate = true;
            }
            if (empty($user->join_date)) {
                $updateData['join_date'] = Carbon::createFromDate(rand(2020, 2025), rand(1, 12), rand(1, 28));
                $needsUpdate = true;
            }
            if (empty($user->employment_status)) {
                $updateData['employment_status'] = $employStatuses[array_rand($employStatuses)];
                $needsUpdate = true;
            }
            if (empty($user->bank_name)) {
                $updateData['bank_name'] = $banks[array_rand($banks)];
                $needsUpdate = true;
            }
            if (empty($user->bank_account_number)) {
                $updateData['bank_account_number'] = (string)rand(1000000000, 9999999999);
                $needsUpdate = true;
            }
            if (empty($user->bank_account_holder)) {
                $updateData['bank_account_holder'] = $user->full_name ?? $user->name;
                $needsUpdate = true;
            }
            if (empty($user->npwp)) {
                $updateData['npwp'] = sprintf('%02d.%03d.%03d.%03d', rand(1,99), rand(1,999), rand(1,999), rand(1,999));
                $needsUpdate = true;
            }
            if (is_null($user->basic_salary) || $user->basic_salary == 0) {
                $updateData['basic_salary'] = rand(3000000, 8000000);
                $needsUpdate = true;
            }
            if (empty($user->skills)) {
                $skillSets = ['Microsoft Office, Komunikasi','Pelayanan Pelanggan, Kasir','Manajemen Stok, Logistik','Sales, Marketing','Admin, Pembukuan','SPG, Pelayanan','Team Leader, Koordinasi','Keuangan, Pembukuan'];
                $updateData['skills'] = $skillSets[array_rand($skillSets)];
                $needsUpdate = true;
            }
            if (empty($user->living_address)) {
                $cities = ['Bekasi','Jakarta','Cikarang','Tambun','Cibitung','Depok','Bogor','Tangerang','Bandung','Karawang'];
                $streets = ['Jl. Raya','Jl. Merdeka','Jl. Ahmad Yani','Jl. Diponegoro','Jl. Sudirman','Jl. Mawar','Jl. Melati','Jl. Anggrek','Jl. Kenanga','Jl. Flamboyan'];
                $updateData['living_address'] = $streets[array_rand($streets)] . ' No.' . rand(1,200) . ', Rt.' . rand(1,10) . '/Rw.' . rand(1,10) . ', ' . $cities[array_rand($cities)];
                $needsUpdate = true;
            }
            if (empty($user->origin)) {
                $origins = ['Bekasi','Jakarta','Cikarang','Tambun','Cibitung','Depok','Bogor','Tangerang','Bandung','Karawang','Purwakarta','Subang','Indramayu','Cirebon','Kuningan'];
                $updateData['origin'] = $origins[array_rand($origins)];
                $needsUpdate = true;
            }
            if (empty($user->phone)) {
                $updateData['phone'] = '08' . rand(1000000000, 9999999999);
                $needsUpdate = true;
            }
            if (empty($user->emergency_contact_name)) {
                $fn = $firstNames[array_rand($firstNames)];
                $ln = $lastNames[array_rand($lastNames)];
                $updateData['emergency_contact_name'] = $fn . ' ' . $ln;
                $needsUpdate = true;
            }
            if (empty($user->emergency_contact_phone)) {
                $updateData['emergency_contact_phone'] = '08' . rand(1000000000, 9999999999);
                $needsUpdate = true;
            }
            if (empty($user->emergency_contact_relation)) {
                $relations = ['Orang Tua','Suami/Istri','Saudara','Teman'];
                $updateData['emergency_contact_relation'] = $relations[array_rand($relations)];
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $user->update($updateData);
                $count++;
            }
        }

        $this->command->info("Updated $count users with complete employee data.");
    }
}
