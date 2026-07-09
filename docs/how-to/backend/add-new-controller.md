---
title: Add a New Controller
diataxis: how-to
owner: Backend Lead
update_frequency: on-change
classification: mandatory
---

# Add a New Controller

## Steps

```bash
# Generate controller
php artisan make:controller Admin/ProductController

# Add resourceful methods
php artisan make:controller Admin/ProductController --resource

# Generate with model binding
php artisan make:controller Admin/ProductController --model=Product
```

## Conventions

- Place in appropriate subdirectory under `app/Http/Controllers/`
- Use constructor injection for services
- Use Form Requests for validation
- Return typed responses (JSON or View)
- Add route in `routes/web.php` or `routes/api.php`

## Example

```php
class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index()
    {
        return view('products.index', [
            'products' => $this->productService->getAll()
        ]);
    }
}
```
