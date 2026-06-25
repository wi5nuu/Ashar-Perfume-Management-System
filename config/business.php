<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Berisi konfigurasi untuk logika bisnis aplikasi seperti threshold approval,
    | rate pajak (PPN), dan category_id untuk produk bonus.
    |
    */

    'approval_threshold' => env('APPROVAL_THRESHOLD', 5000000), // Default: Rp 5.000.000
    
    'tax_rate'           => env('DEFAULT_TAX_RATE', 0.10),      // Default: 10%
    
    'bonus_category_id'  => env('BONUS_CATEGORY_ID', null),     // Category ID untuk produk bonus
];
