---
title: ADR-009 — Service Layer Pattern
status: Accepted
date: 2024-04
deciders: Staff Software Engineer
---

# ADR-009: Service Layer Pattern

## Context

Controllers were growing too large with business logic mixed with HTTP concerns. Need a clean separation between HTTP handling and domain logic.

## Decision

Introduce a Service Layer between Controllers and Models.

## Rationale

- Controllers handle HTTP concerns only (validation, response)
- Services contain business logic (orchestration, computation)
- Services are testable without HTTP context
- Services can be reused across controllers (web + API)
- Single Responsibility Principle applied

## Consequences

- All new business logic goes in Service classes
- Controllers remain thin (inject service, call method, return response)
- Services may depend on Repositories, Actions, and other Services
- No HTTP dependencies in Service layer (Request, Session, etc.)
