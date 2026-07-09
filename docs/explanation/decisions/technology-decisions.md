---
title: Technology Decisions Summary
diataxis: explanation
prerequisites:
  - ADR-001 through ADR-010
owner: Staff Software Engineer
update_frequency: per-new-ADR
classification: recommended
---

# Technology Decisions Summary

## Why These Technologies?

Every technology choice in APMS was made deliberately. Here's the rationale behind the stack:

| Technology | Why | What Problem It Solves | Alternative Considered |
|---|---|---|---|
| **Laravel** | Rapid CRUD, built-in auth/queue/mail, Blade templating, team familiarity | Need a full-featured framework for retail management | Rails, Django, Express |
| **MySQL 8.0** | ACID compliance, JSON columns, CTEs for reporting, mature replication | Need transactional integrity + flexible reporting | PostgreSQL, MariaDB |
| **Redis** | Sub-ms performance, atomic ops, native Laravel support | Sessions/cache must be fast for POS | Memcached, DynamoDB |
| **Sanctum** | Simple token management, session + token auth in one package | Need both web (session) and API (token) auth | Passport, JWT |
| **PHP 8.2** | Performance improvements, typed properties, enums, readonly classes | Modern PHP without framework migration | Hack, Go (rewrite) |
| **ECS (AWS)** | Container orchestration, blue-green deploy, auto-scaling | Need zero-downtime deployment for retail hours | Kubernetes, Elastic Beanstalk |

## Why Not Microservices?

APMS is a single-domain retail system. The team is small (< 5 engineers). Microservices would add:
- Network latency between services
- Distributed transaction complexity
- Deployment coordination overhead
- Debugging difficulty across services

A well-structured monolith with clear bounded contexts and service layer provides the right balance of maintainability and simplicity for the current scale.
