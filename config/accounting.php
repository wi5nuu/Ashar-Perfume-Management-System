<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enterprise Accounting Configuration
    |--------------------------------------------------------------------------
    |
    | Account codes used by the auto-posting engine. These refer to the codes
    | created by the ChartOfAccountSeeder. If an account code is missing the
    | auto-posting engine will skip posting (never block operational flows).
    |
    */

    'enabled' => env('ACCOUNTING_ENABLED', true),

    'revenue_account_code' => '4-101',

    'accounts' => [
        'kas'                  => '1-101',
        'bank'                 => '1-102',
        'receivable_retail'    => '1-103',
        'receivable_wholesale' => '1-104',
        'inventory'            => '1-105',
        'payable_trade'        => '2-101',
        'payable_tax'          => '2-102',
        'payable_salary'       => '2-103',
        'payable_bpjs'         => '2-104',
        'payable_pph21'        => '2-105',
        'payable_thr'          => '2-106',
        'equity_capital'       => '3-101',
        'equity_retained'      => '3-102',
        'equity_drawing'       => '3-103',
        'revenue_retail'       => '4-101',
        'revenue_wholesale'    => '4-102',
        'revenue_other'        => '4-103',
        'cogs'                 => '5-101',
        'expense_salary'       => '5-102',
        'expense_depreciation' => '5-108',
        'expense_other'        => '5-113',
    ],
];
