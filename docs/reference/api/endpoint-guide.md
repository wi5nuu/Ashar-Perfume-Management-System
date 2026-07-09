---
title: API Endpoint Guide
diataxis: reference
standards:
  - OpenAPI 3.x
owner: Backend Lead
update_frequency: per-release
classification: mandatory
---

# API Endpoint Guide

## Web Routes (Blade)

### Dashboard
| Method | Path | Middleware | Purpose |
|---|---|---|---|
| GET | `/` | auth | Dashboard view |
| GET | `/dashboard` | auth | Dashboard index |
| GET | `/api/dashboard/stats` | auth | Live dashboard stats (JSON) |
| GET | `/api/dashboard/comparison` | auth | YoY/MoM comparison (JSON) |

### Owner Routes
| Method | Path | Role | Purpose |
|---|---|---|---|
| GET | `/owner/monitoring` | owner | Owner monitoring page |
| GET | `/wholesale-customers` | owner | Wholesale customer list |
| POST | `/owner/wholesale-customers/{id}/reset-password` | owner | Reset & show password |
| POST | `/owner/wholesale-customers/{id}/update` | owner | Update email/password |
| GET | `/owner/wholesale-customers/{id}/orders` | owner | View customer orders (JSON) |
| GET | `/owner/wholesale-password-requests` | owner | Password reset requests (JSON) |
| POST | `/owner/wholesale-password-requests/{id}/resolve` | owner | Resolve request (JSON) |
| GET | `/owner/customer-accounts` | owner | All wholesale customer accounts |
| GET | `/owner/special` | owner | Special owner page |
| GET | `/owner/ai-dashboard` | owner | AI business insights dashboard |
| GET | `/owner/loyalty` | owner | Loyalty management |
| GET | `/owner/loyalty/customer/{customer}` | owner | Customer loyalty detail |
| POST | `/owner/loyalty/customer/{customer}/adjust` | owner | Manual credit adjustment |
| GET | `/owner/loyalty/redemptions` | owner | Redemption offers |
| POST | `/owner/loyalty/redemptions` | owner | Create redemption |
| POST | `/owner/loyalty/redemptions/{redemption}` | owner | Update redemption |
| GET | `/owner/loyalty/history` | owner | Credit log history |
| POST | `/owner/notifications/{id}/read` | owner | Mark notification read |
| POST | `/owner/notifications/read-all` | owner | Mark all notifications read |

### Wholesale Customer Routes (Public)
| Method | Path | Purpose |
|---|---|---|
| GET | `/wholesale-customer/login` | Login form |
| POST | `/wholesale-customer/login` | Login action (throttle:5,1) |
| POST | `/wholesale-customer/logout` | Logout |
| GET | `/wholesale-customer/forgot-password` | Forgot password form |
| POST | `/wholesale-customer/forgot-password` | Submit reset request (throttle:3,10) |
| GET | `/wholesale-customer/dashboard` | Customer dashboard (auth) |
| GET | `/wholesale-customer/orders` | Order history (auth) |
| GET | `/wholesale-customer/orders/{id}` | Order detail (auth) |
| GET | `/wholesale-customer/history` | Order history with tier progression |
| GET | `/wholesale-customer/loyalty` | Loyalty page |
| GET | `/wholesale-customer/leaderboard` | Referral leaderboard |
| GET | `/wholesale-customer/track` | Track order by invoice |
| GET | `/wholesale-customer/loyalty/redeem/{id}` | Redeem loyalty credits |

### Customer Portal (Token-based, Unauthenticated)
| Method | Path | Purpose |
|---|---|---|
| GET | `/portal/{token}` | Portal dashboard |
| GET | `/portal/{token}/orders` | Order history |
| GET | `/portal/{token}/statement` | Transaction + payment statement |

### Wholesale Management Routes (role: owner,admin_pusat,admin_cabang,manager,warehouse)
| Method | Path | Purpose |
|---|---|---|
| GET | `/wholesale` | List wholesale orders |
| GET/POST | `/wholesale/create` | Create order |
| GET | `/wholesale/{order}` | Order detail |
| PUT | `/wholesale/{order}` | Update order |
| DELETE | `/wholesale/{order}` | Delete order |
| POST | `/wholesale/{order}/confirm` | Confirm (deduct stock) |
| POST | `/wholesale/{order}/process` | Mark in progress |
| POST | `/wholesale/{order}/pack` | Mark packed |
| POST | `/wholesale/{order}/ship` | Mark shipped |
| POST | `/wholesale/{order}/deliver` | Mark delivered |
| POST | `/wholesale/{order}/complete` | Mark completed |
| POST | `/wholesale/{order}/cancel` | Cancel with reason |
| GET | `/wholesale/{order}/print` | Print invoice |

## API-style Routes (web.php, JSON responses)
| Method | Path | Purpose |
|---|---|---|
| GET | `/api/dashboard/stats` | Dashboard statistics |
| GET | `/api/dashboard/comparison` | Performance comparison |
| GET | `/api/products/search` | Product search (AJAX) |
| GET | `/api/inventory/alerts` | Stock alerts |
| POST | `/api/customers` | Create customer |
| GET | `/api/customers/{customer}` | Customer detail |
| POST | `/api/ai/ask` | AI assistant (throttle:30,1) |

## Sanctum API Routes (api.php)
| Method | Path | Purpose |
|---|---|---|
| GET | `/api/products/search` | Product search (Sanctum) |
| GET | `/api/products/{product}` | Product detail |
| POST | `/api/pos/validate-cart` | Validate cart items |
| POST | `/api/pos/calculate-change` | Calculate change |
| GET | `/api/pos/stock/{product}` | Check stock |

## Rate Limiting

| Route Group | Limit | Window |
|---|---|---|
| Owner routes | 100 requests | 1 minute |
| Wholesale customer routes | 30 requests | 1 minute |
| API routes (auth) | 60 requests | 1 minute |
| API routes (auth, high) | 120 requests | 1 minute |
| Login endpoints | 5 requests | 15 minutes |
| AI chat | 30 requests | 1 minute |
| Password reset | 3 requests | 10 minutes |
