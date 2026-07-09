---
title: Create and Run a Database Migration
diataxis: how-to
owner: Backend Lead
update_frequency: on-demand
classification: mandatory
---

# Create and Run a Database Migration

## New Table

```bash
php artisan make:migration create_products_table
```

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('category_id')->constrained();
    $table->decimal('price', 15, 2);
    $table->timestamps();
    $table->softDeletes();
});
```

## Modify Table

```bash
php artisan make:migration add_discount_column_to_products
```

```php
Schema::table('products', function (Blueprint $table) {
    $table->decimal('discount', 15, 2)->default(0)->after('price');
});
```

## Run Migrations

```bash
php artisan migrate
php artisan migrate:fresh   # Drop all tables and re-run (dev only)
php artisan migrate:rollback
php artisan migrate:status
```

## Rules

- Always implement `down()` for rollback safety
- Use `foreignId()->constrained()` for foreign keys
- Never modify a published migration — create a new one
