---
title: Domain Model & Bounded Contexts
diataxis: explanation
owner: Staff Software Engineer
update_frequency: on-change
classification: recommended
---

# Domain Model & Bounded Contexts

## Why Separate Domains?

APMS handles several distinct business capabilities. Rather than building a monolith where everything references everything, we define **bounded contexts** — each with its own internal model and explicit interfaces to others.

## Bounded Contexts

```
POS (Point of Sale)
  │
  ├── Inventory (stock lookup, decrement)
  ├── Customer (loyalty points, tier)
  └── Reporting (transaction data)

Wholesale
  │
  ├── Inventory (stock reservation)
  ├── Customer (wholesale-specific pricing)
  ├── Shipping (courier, tracking)
  └── Notification (order status)

Inventory Management
  │
  ├── POS (stock decrement on sale)
  ├── Wholesale (stock reservation)
  ├── StockRequest (inter-branch transfer)
  └── Reporting (stock movement)
```

## Why Not a Single Model?

A naive single model would have `Product` coupled to everything — pricing rules, stock levels, supplier info, category tree, discounts, bundles. Changes in one area would risk breaking another. Bounded contexts let each domain evolve independently with explicit translations at the boundaries.

## Ubiquitous Language

| Term | POS Context | Wholesale Context |
|---|---|---|
| Customer | Walk-in retail buyer | B2B registered buyer |
| Product | Sold at retail price | Sold at wholesale price |
| Order | Not used (transaction) | Wholesale order |
| Stock | In-store physical stock | Warehouse stock |
| Payment | Immediate (cash/QR) | Transfer, term |
