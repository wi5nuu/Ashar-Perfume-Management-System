---
title: Developer Onboarding Guide
diataxis: tutorial
standards: []
owner: Engineering Manager
update_frequency: quarterly
classification: mandatory
---

# Developer Onboarding Guide

## Week 1: Foundation

### Day 1–2: Environment Setup
1. Complete [Local Environment Setup](setup-local-env.md)
2. Verify `php artisan serve` works
3. Verify `php artisan test` passes
4. Access the application at `http://localhost:8000`
5. Log in with provided test credentials

### Day 3–4: Codebase Orientation
1. Read the [arc42 Architecture Document](../../reference/architecture/arc42.md) — understand the high-level structure
2. Read [Folder Structure](../../reference/backend/folder-structure.md)
3. Read [Coding Standards](../../reference/backend/coding-standards.md)
4. Browse the modules in `/app/` — note the Service/Repository/Action pattern
5. Browse all database migrations to understand the schema evolution

### Day 5–7: First Bug Fix
1. Pick a small bug from the backlog
2. Set up your local development branch
3. Write a failing test first
4. Fix the bug
5. Submit your first Pull Request following [PR guidelines](../../reference/team/pr-template.md)

## Week 2: First Feature

### Day 8–10: Feature Implementation
1. Read [How to Add a New Service](../../how-to/backend/add-new-service.md)
2. Read [How to Add a New API Endpoint](../../how-to/backend/add-new-endpoint.md)
3. Implement a simple CRUD feature end-to-end
4. Include unit and feature tests

### Day 11–12: Deployment Pipeline
1. Read [CI/CD Pipeline Guide](../../how-to/devops/ci-cd-pipeline-guide.md)
2. Read [Zero-Downtime Deploy](../../how-to/devops/zero-downtime-deploy.md)
3. Deploy your feature to the staging environment

### Day 13–14: On-Call Shadowing
1. Review [Incident Response Guide](../../how-to/operations/incident-response-guide.md)
2. Review key runbooks:
   - [Database Failover](../../how-to/operations/runbook-database-failover.md)
   - [Cache Failure](../../how-to/operations/runbook-cache-failure.md)
   - [Queue Backlog](../../how-to/operations/runbook-queue-backlog.md)
3. Shadow an on-call engineer for 2 shifts

## Reference Materials to Bookmark
- [All Laravel Reference Docs](../../reference/laravel/)
- [All Backend Standards](../../reference/backend/)
- [API Specification](../../reference/api/openapi.yaml)
- [Architecture Decisions](../../explanation/decisions/)
