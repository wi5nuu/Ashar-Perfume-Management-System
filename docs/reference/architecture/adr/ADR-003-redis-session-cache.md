---
title: ADR-003 — Session/Cache: Redis
status: Superseded (pending production deployment)
date: 2024-01
deciders: Staff Software Engineer, DevOps Lead
---

# ADR-003: Session/Cache: Redis

## Context

The application needs fast, scalable session storage and caching layer for multi-branch deployment with potential horizontal scaling.

## Decision

Use Redis for both session storage and application cache in production environments (pending production infrastructure setup — currently using file-based cache and database queue driver).

## Rationale

- Sub-millisecond read/write performance
- Atomic operations for inventory/queue
- Built-in TTL/expiry support
- Laravel native support
- ElastiCache managed service available
- Pub/Sub for real-time features (future)

## Consequences

- Production dependency on Redis (must be highly available)
- Session data lost if Redis flushed (mitigation: persistent storage mode)
- Additional ~$80/month infrastructure cost
- Fallback to file cache in development
- **Current state**: Not deployed — session uses `file` driver, cache uses `file` driver, queue uses `database` driver
