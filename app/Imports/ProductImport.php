<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    use \Maatwebsite\Excel\Concerns\Importable;

    public function model(array $row)
    {
        $cat = !empty($row['kategori']??$row['category']??'') ? Category::firstOrCreate(['name' => $row['kategori']??$row['category']]) : null;
        $brand = !empty($row['merk']??$row['brand']??'') ? Brand::firstOrCreate(['name' => $row['merk']??$row['brand']]) : null;

        return new Product([
            'name' => $row['nama'] ?? $row['name'],
            'sku' => $row['sku'] ?? ('IMP-'.strtoupper(substr(md5(uniqid()),0,8))),
            'category_id' => $cat?->id,
            'brand_id' => $brand?->id,
            'purchase_price' => (int)($row['harga_beli']??$row['purchase_price']??0),
            'selling_price' => (int)($row['harga_jual']??$row['selling_price']??0),
            'wholesale_price' => (int)($row['harga_grosir']??$row['wholesale_price']??0),
            'unit' => $row['satuan']??$row['unit']??'pcs',
            'is_active' => true,
            'stock' => 0,
        ]);
    }

    public function rules(): array
    {
        return ['name' => 'required|string|max:255'];
    }
}
