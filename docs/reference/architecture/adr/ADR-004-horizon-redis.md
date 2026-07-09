---
title: ADR-004 — Queue: Laravel Horizon + Redis
status: Superseded (pending production deployment)
date: 2024-02
deciders: Staff Software Engineer, DevOps Lead
---

# ADR-004: Queue: Laravel Horizon + Redis

## Context

The application requires background job processing for order processing, notifications, report generation, and backups.

## Decision

Use Laravel Horizon with Redis as the queue backend (pending production deployment — currently using `database` queue driver with `failover` fallback).

## Rationale

- Horizons provides dashboard for monitoring queue health
- Auto-scaling worker processes based on queue load
- Redis Lists for reliable queue storage
- Failed job management built-in
- Configuration-driven queue-to-worker mapping

## Consequences

- Redis required for queue operation
- Horizon configuration managed via config/horizon.php
- Workers run as ECS service (separate from web)
- Queue dashboard accessible to admin role only
- **Current state**: Not deployed — queue uses `database` driver with `failover` strategy; Horizon not active
