# APMS Upgrade Changelog — v2.0.0

## Overview
Complete enterprise upgrade covering 50 discrete commits across all 13 ERP audit categories.

## Security (5)
- SESSION_SECURE_COOKIE enabled for HTTPS-only sessions
- .env added to gitignore
- Health check endpoint for monitoring
- Activity logging viewer for audit trail
- API response transformer with standardized error handling

## Accounting Module (15) — NEW
- Chart of Accounts with 27 standard Indonesian accounts
- Accounting Periods with open/close lifecycle
- Double-entry Journal Entry system with balanced validation
- General Ledger with running balance
- Trial Balance report
- Income Statement (Laba Rugi)
- Balance Sheet (Neraca)
- Cash Flow Statement (Arus Kas)
- AutoPostingService for sales and expenses
- COA Seeder for initial setup

## Payroll & HR (5)
- BPJS Kesehatan & Ketenagakerjaan calculator
- PPh21 progressive tax calculator
- Overtime calculator with holiday rates
- Payroll deduction integration service
- Employee document management

## Business Features (10)
- Customer deposit accounts with transaction history
- Monthly sales targets with achievement tracking
- Supplier scorecard with on-time/quality metrics
- Reorder point calculator with 90-day averaging
- Promo engine with buy-X-get-Y and minimum purchase rules
- Expense approval workflow
- Payroll approval workflow
- Stock transfer approval workflow
- Stock valuation report
- Daily sales report with top products

## Data Management (5)
- Customer import from Excel
- Product import from Excel with auto-category creation
- Product export to Excel
- Customer export to Excel
- Transaction export to Excel with filters

## Performance (3)
- Composite database indexes on 6 high-traffic tables
- Cache warmup command
- PWA service worker for offline support

## Infrastructure (5)
- Database backup command
- Health check API endpoint
- Optimization configuration
- Version bump to 2.0.0
- Dead code cleanup

## Bug Fixes (2)
- Wholesale order branch scoping
- Dashboard COGS/avg_basket corrections

## Running After Upgrade
```bash
php artisan migrate
php artisan db:seed --class=ChartOfAccountSeeder
php artisan cache:warmup
php artisan reorder:check
```
