---
title: ADR-008 — Blue-Green Deployment
status: Planned (not yet deployed)
date: 2024-03
deciders: DevOps Lead, Staff Software Engineer
---

# ADR-008: Blue-Green Deployment

## Context

The system serves retail operations 12+ hours daily. Minimizing downtime during deployments is critical.

## Decision

Use blue-green deployment strategy with ECS service switching (planned for production deployment — currently deployed via git/manual process).

## Rationale

- Zero-downtime deployments
- Instant rollback capability (switch back to previous environment)
- Smoke tests run against idle environment before switch
- ECS supports multiple task definition revisions natively
- Gradual traffic shift reduces risk

## Consequences

- Double infrastructure cost during deployment window
- Database migrations must be backward-compatible (no destructive changes)
- Long-running migrations require careful planning
- Session data in Redis persists across deployment (no session loss)
- **Current state**: Not yet deployed — system runs without container orchestration
