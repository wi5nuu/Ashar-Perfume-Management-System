---
title: Database Design Decisions
diataxis: explanation
prerequisites:
  - ADR-002
  - reference/database/data-dictionary.md
owner: Database Architect
update_frequency: quarterly
classification: recommended
---

# Database Design Decisions

## Why Soft Deletes Instead of Hard Deletes?

Hard deletes lose data permanently. For a retail system, deleted transactions, products, or customers create problems:
- Tax audit — deleted transactions are invisible
- Historical reporting — year-over-year comparison breaks
- Customer recovery — deleted customer cannot be restored
- Referential integrity — deleting a product breaks historical invoices

Soft deletes add a `deleted_at` column. Records are hidden from queries by default but remain in the database. A scheduled job can hard-delete records older than 90 days.

## Why BIGINT UNSIGNED for Primary Keys?

Perfume businesses can have millions of transactions over years. INT (2.1B max) could theoretically overflow. BIGINT (9.2 quintillion) is future-proof. UNSIGNED doubles the positive range. The storage cost (8 bytes vs 4 bytes) is negligible.

## Why ENUM Columns Instead of Lookup Tables?

ENUMs are used for stable, small value sets: payment methods (6 values), order statuses (9 values), roles (9 values). These rarely change. Lookup tables would add joins for no benefit.

When values may be extended by users (categories, suppliers), we use proper foreign-keyed tables.

## Why VARCHAR(255) as Default String Length?

255 characters is the maximum that can be indexed efficiently in MySQL with utf8mb4. It accommodates common fields (name, email, phone) without excessive storage. Longer text uses TEXT columns.

## Why Composite Indexes on Status + Date?

Most list views query "all pending orders" or "today's transactions". A composite index on `(status, created_at)` covers both the filter and the sort in a single index, avoiding file sorts.
