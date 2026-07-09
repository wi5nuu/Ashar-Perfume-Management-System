# Documentation — APMS (Ashar Parfum Management System)

**Diátaxis mode:** Reference / Explanation  
**Governing standards:** IEEE 29148, ISO/IEC 25010, arc42, C4 Model, OpenAPI 3.x, Diátaxis  
**Owner:** Principal Software Architect  
**Update frequency:** Quarterly

---

## Documentation Structure

This documentation is organized by the **Diátaxis framework** into four quadrants:

| Quadrant | Directory | Purpose | Style |
|---|---|---|---|
| **Tutorials** | `/docs/tutorials/` | Learning-oriented, step-by-step | "Follow along and learn" |
| **How-To Guides** | `/docs/how-to/` | Task-oriented, goal-driven | "Solve a specific problem" |
| **Reference** | `/docs/reference/` | Information-oriented, complete | "Look up the facts" |
| **Explanation** | `/docs/explanation/` | Understanding-oriented | "Understand the reasoning" |

## Quick Navigation

### Getting Started
- [Developer Onboarding](tutorials/developer/onboarding.md) — 2-week onboarding plan
- [Local Environment Setup](tutorials/developer/setup-local-env.md) — First-time setup

### Architecture
- [arc42 Architecture Document](reference/architecture/arc42.md) — Full architecture
- [C4 System Context](reference/architecture/c4-level-1-context.md) — System overview
- [Architecture Decisions](explanation/decisions/) — All ADRs

### Laravel
- [Request Lifecycle](reference/laravel/request-lifecycle.md)
- [Eloquent Models](reference/laravel/eloquent-models.md)
- [Service Layer](reference/laravel/service-layer.md)
- [Queues & Jobs](reference/laravel/queue-job-reference.md)

### API
- [OpenAPI Specification](reference/api/openapi.yaml) — API source of truth
- [API Response Standard](reference/backend/api-response-standard.md)
- [Error Handling](reference/backend/error-handling-strategy.md)

### Database
- [Data Dictionary](reference/database/data-dictionary.md)
- [Migration Strategy](reference/database/migration-strategy.md)
- [Backup & Restore](reference/database/backup-strategy.md)

### Operations
- [Runbooks](how-to/operations/) — Incident response procedures
- [Monitoring](reference/monitoring/metrics-catalog.md)
- [Disaster Recovery](reference/operations/dr-plan.md)

---

## Documentation Standards

Every document in this repository conforms to one or more of:

- **IEEE 29148** — Requirements engineering (SRS, BRD, use cases)
- **ISO/IEC 25010** — Software quality (NFR categorization)
- **arc42** — Architecture documentation (all 12 sections)
- **C4 Model** — Architecture diagrams (Levels 1-4)
- **OpenAPI 3.x** — API specification (source of truth)
- **ADR (Nygard)** — Architecture Decision Records
- **Diátaxis** — Documentation quadrants (this structure)

---

## File Metadata Convention

Every file begins with a YAML front-matter block:

```yaml
---
title: Document Title
diataxis: tutorial | how-to | reference | explanation
standards: [IEEE 29148, ISO/IEC 25010, arc42, C4, OpenAPI 3.x]
owner: Team/Role
update_frequency: quarterly | per-release | on-change | on-demand
classification: mandatory | recommended | optional
---
```

---

## Maturity Level

This documentation targets **Enterprise** maturity. See [Documentation Blueprint](DOCUMENTATION_BLUEPRINT.md) for the full specification including maturity mappings for Startup → SME → Scale-up → Enterprise → FAANG.

---

## Contributing

1. All documentation changes require a PR with at least one reviewer from the owning team
2. Follow the Diátaxis mode for your document type (tutorial ≠ how-to ≠ reference ≠ explanation)
3. Include YAML front-matter with all required fields
4. Run `vale` for prose style checks before submitting
