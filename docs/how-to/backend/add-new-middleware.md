---
title: Add a New Middleware
diataxis: how-to
owner: Backend Lead
update_frequency: on-change
classification: mandatory
---

# Add a New Middleware

## Steps

```bash
# Generate middleware class
php artisan make:middleware CustomMiddleware
```

2. Implement the `handle()` method
3. Register in `Kernel.php`:
   - **Global**: add to `$middleware` array (ordered)
   - **Route**: add to `$routeMiddleware` array with alias
4. Apply to routes:
   ```php
   Route::middleware(['auth', 'custom'])->group(function () {
       // routes here
   });
   ```

## Example

```php
class CustomMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->hasAccess()) {
            abort(403);
        }
        return $next($request);
    }
}
```
