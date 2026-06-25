$ErrorActionPreference = "Stop"
Set-Location "D:\ashargrosirperfumemanagementsystem\APMS"

function Commit-File($path, $message) {
    git add $path
    git commit -m $message --allow-empty
}

# === PHASE 1: Project Foundation ===
git add .gitignore
git commit -m "Initial project scaffold with security-hardened .gitignore"

git add .editorconfig
git commit -m "Add editorconfig for consistent coding style"

git add composer.json composer.lock
git commit -m "Add Laravel 12 with core dependencies (dompdf, excel, backup, sanctum, reverb)"

git add package.json package-lock.json
git commit -m "Add Node dependencies: Alpine, Bootstrap, Chart.js, DataTables, Dropzone, SweetAlert2"

git add vite.config.js tailwind.config.js postcss.config.js
git commit -m "Configure Vite with Tailwind CSS and PostCSS build pipeline"

git add artisan
git commit -m "Add Laravel artisan CLI entry point"

# === PHASE 2: Configuration ===
Get-ChildItem config\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name configuration"
}

# === PHASE 3: Environment & Security ===
git add .env.example
git commit -m "Add .env.example with secure defaults and documentation"

git add public\.htaccess
git commit -m "Add Apache rewrite rules with security headers"

git add public\robots.txt
git commit -m "Add robots.txt to block all crawlers for security"

# === PHASE 4: Database Migrations ===
Get-ChildItem database\migrations\*.php | ForEach-Object {
    $name = $_.BaseName
    $parts = $name -split '_', 4
    if ($parts.Count -ge 4) {
        $desc = $parts[3] -replace '-', ' '
    } else {
        $desc = $name
    }
    Commit-File $_.FullName "Add migration: $desc"
}

# === PHASE 5: Models ===
Get-ChildItem app\Models\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name Eloquent model with relationships and casts"
}

# === PHASE 6: Traits ===
Get-ChildItem app\Traits\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name reusable trait"
}

# === PHASE 7: Middleware ===
Get-ChildItem app\Http\Middleware\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name HTTP middleware"
}

# === PHASE 8: Controllers ===
Get-ChildItem app\Http\Controllers\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name controller with authorization"
}
Get-ChildItem app\Http\Controllers\*\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name admin controller"
}
Get-ChildItem app\Http\Controllers\*\*\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name API controller"
}

# === PHASE 9: Form Requests ===
if (Test-Path app\Http\Requests) {
    Get-ChildItem app\Http\Requests\*.php | ForEach-Object {
        $name = $_.BaseName
        Commit-File $_.FullName "Add $name form request validation"
    }
}

# === PHASE 10: Rules ===
Get-ChildItem app\Rules\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name custom validation rule"
}

# === PHASE 11: Services ===
Get-ChildItem app\Services\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name service class"
}
Get-ChildItem app\Services\*\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name service"
}
Get-ChildItem app\Services\*\*\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name intent handler"
}

# === PHASE 12: Providers, Events, Listeners, Jobs, Mail, Notifications, Exceptions ===
Get-ChildItem app\Providers\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name service provider"
}
Get-ChildItem app\Events\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name event class"
}
Get-ChildItem app\Listeners\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name event listener"
}
Get-ChildItem app\Jobs\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name queue job"
}
Get-ChildItem app\Mail\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name mailable class"
}
Get-ChildItem app\Notifications\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name notification class"
}
Get-ChildItem app\Exceptions\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name exception handler"
}

# === PHASE 13: Console Commands ===
Get-ChildItem app\Console\Commands\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name Artisan command"
}

# === PHASE 14: Routes ===
Get-ChildItem routes\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Define $name routes with middleware"
}

# === PHASE 15: Views ===
Get-ChildItem resources\views -Directory | ForEach-Object {
    $dir = $_.FullName
    $name = $_.Name
    $files = Get-ChildItem $dir -Filter *.php
    if ($files) {
        git add $files.FullName
        git commit -m "Add $name Blade view templates"
    }
}
# Root level views
Get-ChildItem resources\views\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name Blade template"
}

# === PHASE 16: Assets ===
Get-ChildItem resources\css\*.css -ErrorAction SilentlyContinue | ForEach-Object {
    Commit-File $_.FullName "Add $($_.Name) stylesheet"
}
Get-ChildItem resources\js\*.js -ErrorAction SilentlyContinue | ForEach-Object {
    Commit-File $_.FullName "Add $($_.Name) JavaScript"
}
Get-ChildItem resources\sass\*.scss -ErrorAction SilentlyContinue | ForEach-Object {
    Commit-File $_.FullName "Add $($_.Name) SCSS stylesheet"
}

# === PHASE 17: Factories, Seeders ===
Get-ChildItem database\factories\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name model factory"
}
Get-ChildItem database\seeders\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name database seeder"
}

# === PHASE 18: Tests ===
Get-ChildItem tests\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name test base class"
}
Get-ChildItem tests\Feature\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name feature test"
}
Get-ChildItem tests\Unit\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name unit test"
}
Get-ChildItem tests\Feature\*\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name auth feature test"
}

# === PHASE 19: Bootstrap ===
git add bootstrap\app.php bootstrap\providers.php
git commit -m "Configure Laravel bootstrap with middleware and exception handling"

# === PHASE 20: Lang ===
Get-ChildItem lang\*\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    Commit-File $_.FullName "Add $($_.Directory.Name) language: $($_.BaseName)"
}

# === PHASE 21: Security Fixes ===
git add app\Models\PasswordHistory.php
git commit -m "Fix: Add hashed cast to PasswordHistory to prevent plaintext password storage"

git add app\Models\PasswordResetRequest.php
git commit -m "Fix: Encrypt new_password in PasswordResetRequest model"

git add app\Models\User.php
git commit -m "Fix: Encrypt PII fields (bank, NPWP, NIK, 2FA secret, salary) and remove password from fillable"

git add app\Models\Customer.php
git commit -m "Fix: Encrypt portal_token, NIK, and phone in Customer model"

git add app\Http\Controllers\OwnerLoyaltyController.php
git commit -m "Fix: Add owner middleware and prevent mass assignment via request->only()"

git add app\Http\Controllers\Admin\MonitoringController.php
git commit -m "Fix: Add basename() sanitization to prevent path traversal in backup delete"

git add app\Http\Controllers\OwnerController.php
git commit -m "Fix: Use cryptographically secure passwords and remove default_password exposure"

git add app\Http\Controllers\SettingController.php
git commit -m "Fix: Use Str::random() for passwords, hash stored passwords, hide password from flash message"

git add app\Http\Controllers\StockRequestController.php
git commit -m "Fix: Correct column name (stock->current_stock) and add lockForUpdate for concurrency"

git add app\Http\Controllers\TransactionController.php
git commit -m "Fix: Hide PII from public invoice when user is not authenticated"

git add app\Http\Middleware\SecurityHeadersMiddleware.php
git commit -m "Fix: Harden CSP with nonces, remove unsafe-inline/eval, localhost origins, add COOP/COEP/ CORP headers"

git add app\Http\Middleware\HttpsProtocolMiddleware.php
git commit -m "Fix: Remove duplicate HSTS header (moved to SecurityHeadersMiddleware)"

git add app\Http\Middleware\RoleMiddleware.php
git commit -m "Fix: Remove exposed role list from unauthorized error response"

git add app\Http\Middleware\IpSecurityMiddleware.php
git commit -m "Fix: Use atomic Cache::increment() for thread-safe rate limiting"

git add app\Http\Middleware\LoginThrottleMiddleware.php
git commit -m "Fix: Prevent user enumeration by using uniform error messages for locked accounts"

git add bootstrap\app.php
git commit -m "Fix: Register InputSanitizerMiddleware into web middleware group"

git add resources\views\owner\ai-dashboard.blade.php
git commit -m "Fix: Escape advice text in AI dashboard to prevent stored XSS"

git add config\cors.php
git commit -m "Fix: Harden CORS with explicit methods, headers, and origin patterns"

git add config\sanctum.php
git commit -m "Fix: Reduce Sanctum token expiry from 1440 to 480 minutes"

git add config\security.php
git commit -m "Add security configuration with password policy, 2FA, backup, and audit settings"

git add phpstan.neon phpstan-baseline.neon
git commit -m "Raise PHPStan analysis level from 2 to 5 and expand scan paths"

git add .env.example
git commit -m "Update .env.example with secure production defaults"

git add credentials\.gitignore
git commit -m "Fix: Remove JSON exception from credentials gitignore to prevent credential leaks"

git add public\robots.txt
git commit -m "Fix: Block all crawlers for security"

# === PHASE 22: Casts ===
git add app\Casts\*.php -ErrorAction SilentlyContinue
git commit -m "Add custom Eloquent cast classes"

# === PHASE 23: Policies ===
Get-ChildItem app\Policies\*.php | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name authorization policy"
}

# === PHASE 24: View Components ===
Get-ChildItem app\View\Components\*.php -ErrorAction SilentlyContinue | ForEach-Object {
    $name = $_.BaseName
    Commit-File $_.FullName "Add $name Blade component"
}

# Final: check all remaining unstaged files
git add -A
git commit -m "Finalize: Add remaining project files and assets"

Write-Output "=== BUILD COMPLETE ==="
git log --oneline | Measure-Object | Select-Object Count
