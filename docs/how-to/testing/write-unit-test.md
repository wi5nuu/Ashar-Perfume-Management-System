---
title: Write a Unit Test
diataxis: how-to
owner: QA Lead
update_frequency: on-demand
classification: mandatory
---

# Write a Unit Test

## Steps

```bash
# Generate test file
php artisan make:test ProductServiceTest --unit
```

## Example

```php
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_product_with_valid_data()
    {
        $service = app(ProductService::class);

        $product = $service->create([
            'name' => 'Test Perfume',
            'price' => 50000,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Perfume',
        ]);
    }
}
```

## Run Tests

```bash
php artisan test                              # All tests
php artisan test --filter=ProductServiceTest  # Specific file
php artisan test --parallel                   # Parallel execution
```

## Best Practices

- Test behavior, not implementation
- Use factories for model creation
- Use `RefreshDatabase` or `DatabaseTransactions`
- Mock external services (WhatsApp, RajaOngkir)
- One assertion method per test
