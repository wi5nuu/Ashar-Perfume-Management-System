---
title: ADR-002 — Database: MySQL 8.0
status: Accepted
date: 2024-01
deciders: Staff Software Engineer, Database Architect
---

# ADR-002: Database: MySQL 8.0

## Context

The system needs a reliable relational database for transactional data (sales, inventory, customers) with support for complex joins, aggregations, and reporting.

## Decision

Use MySQL 8.0 as the primary database engine with InnoDB storage engine.

## Rationale

- Widely supported by hosting providers and cloud (RDS)
- Strong ACID compliance for financial transactions
- JSON column support for flexible data (customer details, product variants)
- Full-text search for product search
- Mature replication features for HA
- CTE and window functions for reporting queries

## Consequences

- Schema migrations managed via Laravel migration system
- Read replicas for reporting queries in production
- Connection pooling via PDO (default)

## Compliance

- All tables use InnoDB engine
- Primary keys are BIGINT UNSIGNED AUTO_INCREMENT
- Foreign keys defined at database level
- Character set: utf8mb4, collation: utf8mb4_unicode_ci
