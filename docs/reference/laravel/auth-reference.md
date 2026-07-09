---
title: Authentication & Authorization Reference
diataxis: reference
standards:
  - arc42 §8
owner: Staff Software Engineer
update_frequency: on-change
classification: mandatory
---

# Authentication & Authorization Reference

## Authentication

| Detail | Implementation |
|---|---|
| **Guard** | `web` (session-based) |
| **API Auth** | Laravel Sanctum (token-based, prefix `apms_`, 480min expiry) |
| **Customer Portal** | Token-based (48-char portal token linked to Customer, 30-day expiry) |
| **Session Driver** | `file` (default) |
| **Cache Driver** | `file` (default) |
| **Queue Driver** | `database` with `failover` fallback |
| **Password Hash** | bcrypt, cost factor 12 |
| **Login Throttle** | 5 attempts per 15 minutes per IP |
| **Password Policy** | Min 10 chars, uppercase, lowercase, number, special char, history 5, max age 90 days |

### User Roles

| Role | Description |
|---|---|
| `owner` | Full system access |
| `admin_pusat` | Central office admin |
| `admin_cabang` | Branch-level admin |
| `warehouse` | Inventory operations |
| `employee` | Limited employee access |
| `wholesale_customer` | Customer portal access |

## Authorization

### Gates (Ability-based)

| Gate | Description |
|---|---|
| `owner` | Owner-only operations |
| `inventory.view` | View inventory |
| `inventory.manage` | Adjust inventory |
| `expenses.view` | View expenses |
| `expenses.manage` | Create/edit expenses |
| `reports.view` | View reports |
| `employees.manage` | Manage employee accounts |
| `wholesale.manage` | Manage wholesale operations |

### Policies (Model-based)

Only these 3 policies exist in the codebase:

| Policy | Model | Abilities |
|---|---|---|
| `TransactionPolicy` | Transaction | viewAny, view, create, update, delete |
| `WholesaleOrderPolicy` | WholesaleOrder | viewAny, view, create, update, delete, confirm, process, pack, ship, deliver, cancel, print |
| `ExpensePolicy` | Expense | viewAny, view, create, update, delete |

### External Auth (Google OAuth)

**Present in codebase** — `WholesaleGoogleAuthController` handles Google OAuth for wholesale customer login via routes `/auth/google` and `/auth/google/callback`. Package: `laravel/socialite`.

### Customer Portal (Public)

**Unauthenticated** — uses a 48-character portal token (stored encrypted on `Customer` model, regenerable by owner). Accessible via `/portal/{token}/*` routes. Token expires after 30 days.

### Permission Check Pattern

```php
// Gate (no model)
Gate::authorize('owner');

// Policy (model-specific)
$this->authorize('update', $transaction);

// Blade directive
@can('inventory.view')
    {{-- Show inventory section --}}
@endcan
```

### RBAC via Roles & Permissions

The system uses custom RBAC tables (`roles`, `permissions`, `permission_role`, `permission_user`, `role_user`) managed by `RbacService` and `RbacController`. Gates are registered dynamically via `RbacService::registerGates()`.
