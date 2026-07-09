---
title: ADR-006 — Role-Based Access: Custom Middleware
status: Accepted
date: 2024-02
deciders: Staff Software Engineer
---

# ADR-006: Role-Based Access: Custom Middleware

## Context

The system has 6 user roles with distinct access levels. Need a flexible authorization mechanism that can handle multi-role access and role hierarchies.

## Decision

Use custom `RoleMiddleware` class (aliased as `role` in `bootstrap/app.php`) with configurable role arrays, combined with Laravel Policies for model-level authorization. Implemented alongside RBAC via `roles`/`permissions` database tables.

## Rationale

- Middleware handles route-level access (which roles can access which pages)
- Policies handle model-level access (which user can CRUD which entity)
- Separation of concerns: routing vs domain authorization
- Configurable via `role:{roles}` middleware alias (registered in `bootstrap/app.php`)
- Role hierarchy built into middleware logic

## Consequences

- Two authorization layers must be maintained (routes + policies)
- New roles require updates to middleware configurations
- Role hierarchy documented in auth-reference.md
- Actual roles: `owner`, `admin_pusat`, `admin_cabang`, `warehouse`, `employee`, `wholesale_customer`
