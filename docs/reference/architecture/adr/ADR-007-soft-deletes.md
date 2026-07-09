---
title: ADR-007 — Soft Deletes for Key Entities
status: Accepted
date: 2024-03
deciders: Staff Software Engineer, Database Architect
---

# ADR-007: Soft Deletes for Key Entities

## Context

Business data (products, customers, transactions) must be recoverable after accidental deletion. Audit trail must track when and by whom data was "deleted".

## Decision

Use Laravel's `SoftDeletes` trait on key business entities, retaining records indefinitely.

## Rationale

- Data recovery without database restore
- Audit trail for compliance
- Laravel native support (deleted_at column)
- All queries in admin/owner panels use `withTrashed()`
- "Deleted" records filtered out from customer-facing views

## Consequences

- Additional storage for soft-deleted records
- All relationships must handle trashed records
- Most queries include `whereNull('deleted_at')` implicitly
- Hard delete policy: After 90 days via scheduled task (configurable)
