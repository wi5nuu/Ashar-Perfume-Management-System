<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation
{
    use \Maatwebsite\Excel\Concerns\Importable;

    public function model(array $row)
    {
        return new Customer([
            'name' => $row['nama'] ?? $row['name'],
            'phone' => $row['telepon'] ?? $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'address' => $row['alamat'] ?? $row['address'] ?? null,
            'type' => $row['tipe'] ?? $row['type'] ?? 'umum',
            'branch_id' => auth()->user()->branch_id,
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return ['name' => 'required|string|max:255'];
    }
}
