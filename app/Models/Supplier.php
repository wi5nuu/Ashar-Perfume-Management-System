<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * supplier_id ada di tabel inventories dan juga di tabel products.
     * Relasi ke inventories untuk memetakan produk yang disupply.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Produk yang pernah disupply (via inventories) — hasManyThrough
     */
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Inventory::class,
            'supplier_id',  // inventories.supplier_id
            'id',           // products.id
            'id',           // suppliers.id
            'product_id'    // inventories.product_id
        );
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierPrice::class);
    }
}
