---
title: Data Dictionary
diataxis: reference
standards: []
owner: Database Architect
update_frequency: per-migration
classification: mandatory
---

# Data Dictionary

## Core Tables

### `users`

Full model with **47 fillable fields** including employee data (religion, marital_status, last_education, bank details, NPWP), housing (is_staying_in_mess), referral tracking. Uses `SoftDeletes`, `HasApiTokens` (Sanctum).

Key fields: name, email, password, phone, role, branch_id, is_active, can_login, nik (encrypted), bank_account_number (encrypted), npwp (encrypted), two_factor_secret (encrypted), referral_code, referred_by_id.

### `transactions`

| Column | Type | Description |
|---|---|---|
| id | BIGINT UNSIGNED AUTO_INCREMENT | Primary key |
| invoice_number | VARCHAR(50) UNIQUE | Invoice number |
| customer_id | BIGINT UNSIGNED NULLABLE | FK to customers |
| customer_type | ENUM('retail','wholesale') | Customer category |
| user_id | BIGINT UNSIGNED | FK to users (cashier) |
| branch_id | BIGINT UNSIGNED NULLABLE | FK to branches |
| subtotal | DECIMAL(15,2) | Before discount |
| discount | DECIMAL(15,2) | Discount amount |
| discount_type | ENUM('fixed','percent') NULLABLE | Discount type |
| discount_percent | DECIMAL(5,2) NULLABLE | Percent if type=percent |
| tax_amount | DECIMAL(15,2) | PPN tax |
| total_amount | DECIMAL(15,2) | Total after discount |
| final_amount | DECIMAL(15,2) | Final after tax |
| paid_amount | DECIMAL(15,2) | Amount paid |
| change_amount | DECIMAL(15,2) | Change returned |
| payment_method | ENUM('cash','qris','transfer','ewallet','debit_card','credit_card') | Payment method |
| payment_status | ENUM('paid','unpaid','partial') | Payment status |
| debt_amount | DECIMAL(15,2) DEFAULT 0 | Debt portion |
| coupon_id | BIGINT UNSIGNED NULLABLE | FK to coupons |
| receipt_visibility | ENUM('visible','hidden') DEFAULT 'visible' | Customer receipt access |
| tax_enabled | BOOLEAN DEFAULT TRUE | Whether PPN applied |
| notes | TEXT NULLABLE | Transaction notes |
| created_at | TIMESTAMP | Transaction timestamp |
| deleted_at | TIMESTAMP NULLABLE | Soft delete |

Uses `SoftDeletes`. Relationships: customer, user, branch, details (HasMany TransactionDetail), coupon, debtPayments.

### `wholesale_orders`

Status enum: `pending`, `reviewed`, `on_progress`, `packed`, `shipped`, `delivered`, `completed`, `cancelled`. Tracks confirmed_at, reviewed_at, packed_at, shipped_at, delivered_at, cancelled_at, completed_at timestamps. Uses `SoftDeletes`.

Key columns: invoice_number, user_id, branch_id, customer_id, total_amount, shipping_cost, status, recipient_name/phone/address, shipping_courier, tracking_number, handler_id (FK to users), packing_days, estimated_arrival, cancellation_reason.

### `inventories`

Tracks per-product, per-branch stock. Supports warehouse_id (nullable), supplier_id, batch_number, expiration_date, cost_per_unit, bulk_stock_ml (for refill products). Relationships: product, branch, warehouse, supplier, movements.

### `products`

Key columns: name, barcode, product_category_id, brand, size, unit, purchase_price (hidden), selling_price, wholesale_price, initial_stock, image, is_active, track_inventory, supplier_id, minimum_stock, is_refill, refill_price_per_ml.

Does NOT use SoftDeletes (unlike many other models). Uses `HasFactory`.

### Other Key Models (48 total models)

| Model | Key Traits | Notes |
|---|---|---|
| Branch | — | Operational hours, shift_start/end, coordinates |
| Customer | — | Encrypted portal_token, loyalty_rank, credits, lifetime_spend |
| ProductCategory | HasFactory | Has color field |
| Supplier | — | Name + contact hidden fields |
| Warehouse | — | Per-branch warehouses |
| TransactionDetail | — | Includes bonus_quantity, refill_volume_ml |
| Shift | — | Cash reconciliation, closing photo, denominations |
| Attendance | — | Time in/out, role tracking |
| Expense | SoftDeletes | Per-category, per-branch |
| ExpenseCategory | — | Simple name-only model |
| DebtPayment | SoftDeletes | Transaction debt payments |
| InventoryMovement | — | Polymorphic reference, stock before/after |
| StockRequest | — | Inter-branch transfer workflow |
| StockAudit | — | Physical inventory counts |
| SalesReturn | — | Return workflow with approval |
| PurchaseOrder | — | Supplier purchase with receive workflow |
| GoodsReceipt | — | Direct stock receipt |
| WholesaleProduct | SoftDeletes | Separate wholesale catalog |
| WholesaleOrderDetail | HasFactory | Links to Product or WholesaleProduct |
| WholesaleCreditLog | — | Loyalty credit history (morphTo reference) |
| WholesaleRedemption | — | Redeemable rewards |
| Role | — | RBAC roles |
| Permission | — | RBAC permissions |
| AuditLog | — | Activity audit trail |
| LoginActivity | — | Login tracking |
| KnownDevice | — | Device fingerprinting |
| IpBlacklist | — | Brute-force protection |
| PasswordHistory | — | Last 5 passwords |
| PasswordResetRequest | — | Encrypted new_password |
| Setting | — | Key-value store |
| Commission | — | Employee commissions |
| Payroll | — | Monthly payroll records |
| PayrollSetting | — | Per-employee allowance/deduction |
| PriceHistory | — | Product price change log |
| AiUnansweredQuery | — | Unanswered AI queries |
| Coupon | — | Discount coupons per customer |

## Payment Methods

From config: `cash`, `qris`, `transfer`, `ewallet`, `debit_card`, `credit_card`

## PPN (Tax)

Rate: **10%** (configurable via `config/business.php`). Toggle per transaction via `tax_enabled` boolean.

## Localization

Timezone: `Asia/Jakarta` (WIB). Locale: `en`. Cipher: `AES-256-GCM`.
