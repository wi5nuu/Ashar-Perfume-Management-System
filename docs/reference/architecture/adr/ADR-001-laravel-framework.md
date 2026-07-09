---
title: ADR-001 — Use Laravel as Primary Framework
status: Accepted
date: 2024-01
deciders: Staff Software Engineer, Project Lead
---

# ADR-001: Use Laravel as Primary Framework

## Context

The APMS system requires a full-featured web application framework for rapid development of retail management features including POS, inventory, wholesale, and reporting.

## Decision

Use Laravel PHP framework (current: 12.x) as the primary application framework.

## Rationale

- Team's existing PHP expertise
- Built-in ORM (Eloquent) for complex querying
- Blade templating for server-side rendered views
- Queue system, mail, notifications built-in
- Sanctum for API auth
- Active ecosystem (Spark, Horizon, Telescope, Cashier)
- Strong migration and schema building tools

## Consequences

- Application is PHP-bound (not easily portable to other languages)
- Developer hiring pool includes PHP developers
- Composer dependency management required
- Regular framework upgrades needed for security patches

## Compliance

- Laravel version >= 12.x required
- No bypassing Eloquent for raw SQL (except complex reporting queries documented in code)
