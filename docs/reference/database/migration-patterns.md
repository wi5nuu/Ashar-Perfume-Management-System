---
title: Migration Patterns
diataxis: reference
standards:
  - arc42 §6
owner: Database Architect
update_frequency: on-change
classification: mandatory
---

# Migration Patterns

## Naming Convention

```
YYYY_MM_DD_HHmmss_description_table_name.php
```

Examples:
- `2025_01_15_000001_create_transactions_table.php`
- `2025_01_20_093012_add_shipping_address_to_wholesale_orders.php`
- `2025_02_01_141500_add_foreign_key_constraints_to_transactions.php`

## Structural Patterns

### Create Table

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number', 50)->unique();
    $table->foreignId('customer_id')->nullable()->constrained();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('branch_id')->nullable()->constrained();
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->decimal('total_amount', 15, 2)->default(0);
    $table->enum('payment_method', ['cash','qris','debit','credit','transfer','ewallet']);
    $table->enum('payment_status', ['paid','unpaid','partial'])->default('paid');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

### Modify Table (Add Column)

```php
Schema::table('wholesale_orders', function (Blueprint $table) {
    $table->text('shipping_address')->after('recipient_phone');
});
```

### Modify Table (Add Index)

```php
Schema::table('transactions', function (Blueprint $table) {
    $table->index('branch_id');
    $table->index(['payment_status', 'created_at']);
});
```

### Drop Column

```php
Schema::table('products', function (Blueprint $table) {
    $table->dropColumn('old_field');
});
```

## Migration Rules

- **Never** modify a published migration (create a new one)
- **Down methods** must be implemented for rollback safety
- **Batch structural changes** in a single migration when possible
- **Foreign keys** are added after both tables exist
- **Indexes** on frequently queried columns (foreign keys, status, dates)
- **Enums** must be listed in `App\Enums\{Name}Enum` for reference
