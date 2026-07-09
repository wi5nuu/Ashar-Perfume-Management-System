---
title: Entity Relationship Summary
diataxis: reference
standards:
  - arc42 §6
owner: Database Architect
update_frequency: per-migration
classification: mandatory
---

# Entity Relationship Summary

```mermaid
erDiagram
    USERS ||--o{ TRANSACTIONS : "creates"
    USERS ||--o{ WHOLESALE_ORDERS : "places"
    USERS ||--o{ INVENTORY_ADJUSTMENTS : "performs"
    USERS }|--|| BRANCHES : "belongs_to"

    BRANCHES ||--o{ INVENTORIES : "has"
    BRANCHES ||--o{ TRANSACTIONS : "records"
    BRANCHES ||--o{ EXPENSES : "incurs"
    BRANCHES ||--o{ CUSTOMERS : "serves"
    BRANCHES ||--o{ STOCK_REQUESTS : "participates"

    PRODUCTS ||--o{ INVENTORIES : "stocked_as"
    PRODUCTS }|--|| CATEGORIES : "categorized_as"
    PRODUCTS }|--o{ SUPPLIERS : "supplied_by"
    PRODUCTS ||--o{ TRANSACTION_ITEMS : "included_in"
    PRODUCTS ||--o{ WHOLESALE_ORDER_ITEMS : "included_in"

    INVENTORIES ||--o{ INVENTORY_ADJUSTMENTS : "adjusted_by"

    TRANSACTIONS ||--o{ TRANSACTION_ITEMS : "contains"
    TRANSACTIONS }|--o{ CUSTOMERS : "belongs_to"

    WHOLESALE_ORDERS ||--o{ WHOLESALE_ORDER_ITEMS : "contains"
    WHOLESALE_ORDERS }|--o{ CUSTOMERS : "belongs_to"

    STOCK_REQUESTS }|--|| BRANCHES : "from_branch"
    STOCK_REQUESTS }|--|| BRANCHES : "to_branch"
    STOCK_REQUESTS ||--o{ STOCK_REQUEST_ITEMS : "contains"

    CUSTOMERS ||--o{ LOYALTY_REDEMPTIONS : "redeems"
    CUSTOMERS }|--|| LOYALTY_TIERS : "classified_as"

    WHOLESALE_CUSTOMER_DETAILS ||--|| USERS : "extends"
    WHOLESALE_CUSTOMER_DETAILS }|--|| WHOLESALE_LOYALTY_TIERS : "classified_as"
```

## Polymorphic Relationships

| Table | Morph Column 1 | Morph Column 2 | Target |
|---|---|---|---|
| `notifications` | `notifiable_type` | `notifiable_id` | Users, WholesaleCustomers |
| `activity_logs` | `subject_type` | `subject_id` | Various models |
| `attachments` | `attachable_type` | `attachable_id` | Transactions, Products |

## Soft Delete Rules

- Models with `SoftDeletes`: Products, Users, Categories, Suppliers, WholesaleOrders
- All queries for owner/admin panels use `withTrashed()`
- Deleting a parent cascades via model event, NOT DB constraint
